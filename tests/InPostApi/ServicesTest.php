<?php

namespace Xgrz\InPost\Tests\InPostApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Jobs\UpdateInPostServicesJob;
use Xgrz\InPost\Models\InPostAdditionalService;
use Xgrz\InPost\Models\InPostService;
use Xgrz\InPost\Tests\InPostTestCase;

class ServicesTest extends InPostTestCase
{
    use RefreshDatabase;

    private function setupServices(): void
    {
        Http::fake($this->fakeServicesResponse());
        (new UpdateInPostServicesJob)->handle();
    }

    public function test_api_call_to_fetch_services()
    {
        Http::fake($this->fakeServicesResponse());
        InPost::services()->getFromInPost();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/services');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_can_get_list_of_services()
    {
        self::setupServices();

        $this->assertDatabaseCount('inpost_services', 15);
        $this->assertInstanceOf(InPostService::class, InPost::services()->list()->first());
    }

    public function test_can_get_list_of_available_services()
    {
        self::setupServices();
        InPostService::inRandomOrder()->first()->update(['active' => false]);

        $this->assertDatabaseCount('inpost_services', 15);
        $this->assertCount(14, InPost::services()->available());
    }

    public function test_can_get_list_of_additional_services()
    {
        self::setupServices();
        $service = InPost::services()->list()->first();

        $this->assertDatabaseCount('inpost_additional_services', 59);
        $this->assertInstanceOf(
            InPostAdditionalService::class,
            InPost::services()
                ->additionalList($service)
                ->first()
        );
    }

    public function test_can_get_list_of_available_additional_services()
    {
        self::setupServices();
        $service = InPostService::find('inpost_courier_standard');

        $additionalServicesCount = InPostAdditionalService::query()
            ->where('inpost_service_id', $service->id)
            ->count();

        InPostAdditionalService::query()
            ->where('inpost_service_id', $service->id)
            ->where('active', true)
            ->first()
            ->update(['active' => false]);

        $this->assertCount(
            --$additionalServicesCount,
            InPost::services()->additionalAvailable($service)
        );
    }

    public function test_can_toggle_service_availability()
    {
        self::setupServices();
        $service = InPostService::first();

        $this->assertTrue($service->active);
        InPost::services()->changeServiceAvailability($service);

        $this->assertFalse($service->refresh()->active);
    }

    public function test_can_disable_service_availability()
    {
        self::setupServices();
        $service = InPostService::first();

        $this->assertTrue($service->active);
        InPost::services()->changeServiceAvailability($service, false);

        $this->assertFalse($service->refresh()->active);
    }

    public function test_can_enable_service_availability()
    {
        self::setupServices();
        $service = InPostService::first();
        $service->update(['active' => false]);

        $this->assertFalse($service->refresh()->active);
        InPost::services()->changeServiceAvailability($service, true);
        $this->assertTrue($service->refresh()->active);
    }

    public function test_can_toggle_additional_service_availability()
    {
        self::setupServices();
        $additionalService = InPostAdditionalService::first();

        $this->assertTrue($additionalService->active);

        InPost::services()->changeAdditionalServiceAvailability($additionalService);
        $this->assertFalse($additionalService->refresh()->active);
    }

    public function test_can_disable_additional_service_availability()
    {
        self::setupServices();
        $additionalService = InPostAdditionalService::first();
        $additionalService->update(['active' => false]);

        $this->assertFalse($additionalService->refresh()->active);
        InPost::services()->changeAdditionalServiceAvailability($additionalService, true);
        $this->assertTrue($additionalService->refresh()->active);
    }

    public function test_can_enable_additional_service_availability()
    {
        self::setupServices();
        $additionalService = InPostAdditionalService::first();
        $additionalService->update(['active' => true]);

        $this->assertTrue($additionalService->refresh()->active);
        InPost::services()->changeAdditionalServiceAvailability($additionalService, false);
        $this->assertFalse($additionalService->refresh()->active);
    }
}
