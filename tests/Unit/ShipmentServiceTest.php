<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\DTOs\Services\ShipmentService;
use Xgrz\InPost\Exceptions\ShipXException;
use Xgrz\InPost\Jobs\UpdateInPostServicesJob;
use Xgrz\InPost\Models\InPostAdditionalService;
use Xgrz\InPost\Models\InPostService;
use Xgrz\InPost\Tests\InPostTestCase;

class ShipmentServiceTest extends InPostTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake($this->fakeServicesResponse());
        (new UpdateInPostServicesJob)->handle();
    }

    public function test_can_set_service_name()
    {
        $service = ShipmentService::make();
        $service->setService('inpost_courier_standard');

        $this->assertArrayHasKey('service', $service->payload());
        $this->assertEquals('inpost_courier_standard', $service->payload()['service']);;
    }

    public function test_cannot_set_unknown_service_name_and_exception_is_thrown()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage('Service not found: [not_existing_service]');

        $service = ShipmentService::make();
        $service->setService('not_existing_service');
    }

    public function test_cannot_set_disabled_service_name_and_exception_is_thrown()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage('Service [inpost_courier_standard] is locally disabled');

        InPostService::find('inpost_courier_standard')->update(['active' => false]);
        $service = ShipmentService::make();
        $service->setService('inpost_courier_standard');
    }

    public function test_can_set_additional_service_name()
    {
        $service = ShipmentService::make();
        $service
            ->setService('inpost_courier_standard')
            ->additionalServices('SMS');

        $this->assertArrayHasKey('additional_services', $service->payload());
        $this->assertTrue(in_array('sms', $service->payload()['additional_services']));
    }

    public function test_can_set_additional_service_names_with_array()
    {
        $service = ShipmentService::make();
        $service
            ->setService('inpost_courier_standard')
            ->additionalServices(['SMS', 'email']);

        $this->assertArrayHasKey('additional_services', $service->payload());
        $this->assertTrue(in_array('sms', $service->payload()['additional_services']));
        $this->assertTrue(in_array('email', $service->payload()['additional_services']));
    }


    public function test_throws_exception_when_service_is_not_provided_and_additional_service_has_been_set()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage('Service not set. Use setService() method first.');

        ShipmentService::make()->additionalServices('SMS');
    }

    public function test_throws_exception_when_unknown_additional_service_is_set()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage('Additional service not found [unknown_service]');

        $service = ShipmentService::make();
        $service
            ->setService('inpost_courier_standard')
            ->additionalServices(['sms', 'unknown_service']);
    }

    public function test_throws_exception_when_unknown_additional_service_is_disabled()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage('Additional service [sms] is locally disabled for selected inpost_courier_standard service');

        InPostAdditionalService::where('ident', 'sms')
            ->where('inpost_service_id', 'inpost_courier_standard')
            ->update(['active' => false]);

        $service = ShipmentService::make();
        $service
            ->setService('inpost_courier_standard')
            ->additionalServices('sms');
    }

    public function test_can_set_target_point()
    {
        $service = ShipmentService::make();
        $service->targetPoint('WA375M');

        $this->assertArrayHasKey('custom_attributes', $service->payload());;
        $this->assertArrayHasKey('target_point', $service->payload()['custom_attributes']);;
    }
}
