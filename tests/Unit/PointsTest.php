<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Enums\PointStatus;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Jobs\SynchronizeInPostPointsJob;
use Xgrz\InPost\Jobs\UpdateInPostPointDataJob;
use Xgrz\InPost\Models\InPostPoint;
use Xgrz\InPost\Tests\InPostTestCase;

class PointsTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_api_call_to_fetch_points_without_query()
    {
        Http::fake($this->fakePointsResponse());
        InPost::points();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/points');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_api_call_to_fetch_points_with_name_search()
    {
        Http::fake($this->fakePointsResponse());
        InPost::points(['name' => 'WAW365']);

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/points?name=WAW365');;
    }

    public function test_api_call_to_fetch_points_with_multiple_values_search()
    {
        Http::fake($this->fakePointsResponse());
        InPost::points(['name' => ['WAW365', 'WAW366']]);

        Http::assertSent(fn($request) => urldecode($request->url()) === 'https://sandbox-api-shipx-pl.easypack24.net/v1/points?name=WAW365,WAW366');
    }

    public function test_can_synchronize_process_dispatch_update_jobs()
    {
        Queue::fake();
        Http::fake($this->fakePointsResponse());

        (new SynchronizeInPostPointsJob)->handle();

        Queue::assertPushed(UpdateInPostPointDataJob::class);
    }

    public function test_update_job()
    {
        Http::fake($this->fakePointsResponse());
        (new SynchronizeInPostPointsJob)->handle();

        $point = InPostPoint::name('BDA012')->first();

        $this->assertDatabaseCount('inpost_points', 3);
        $this->assertDatabaseHas('inpost_points', ['name' => 'ADA01M']);
        $this->assertDatabaseHas('inpost_points', ['name' => 'BDA012']);
        $this->assertDatabaseHas('inpost_points', ['name' => 'CDA012']);

        $this->assertNotNull($point);
        $this->assertNotEmpty($point->image_url);
        $this->assertNotEmpty($point->name);
        $this->assertNotEmpty($point->status);
        $this->assertInstanceOf(PointStatus::class, $point->status);
        $this->assertNotEmpty($point->latitude);
        $this->assertNotEmpty($point->longitude);
        $this->assertNotEmpty($point->location_type);
        $this->assertNotEmpty($point->location_description);
        $this->assertNotEmpty($point->street);
        $this->assertNotEmpty($point->city);
        $this->assertNotEmpty($point->post_code);
        $this->assertSame('21-412', $point->post_code);
        $this->assertNotEmpty($point->payment_available);
        $this->assertIsBool($point->payment_available);
        $this->assertNotEmpty($point->payment_point_description);
        $this->assertNotEmpty($point->payment_type);
        $this->assertNotEmpty($point->functions);
        $this->assertIsArray($point->functions);
        $this->assertNotEmpty($point->location_247);
        $this->assertIsBool($point->location_247);
        $this->assertNotNull($point->partner_id);

        $this->assertNotEmpty($point->physical_type_mapped);
    }
}
