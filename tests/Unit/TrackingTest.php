<?php

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class TrackingTest extends InPostTestCase
{

    public function test_api_call_to_track_shipment()
    {
        Http::fake($this->fakeTrackingResponse());
        InPost::trackingInfo('123456789');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/tracking/123456789');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_api_call_to_track_shipment_events_only()
    {
        Http::fake($this->fakeTrackingResponse());
        $events = InPost::trackingEvents('123456789');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/tracking/123456789');
        $this->assertIsArray($events);
        $this->assertArrayHasKey('origin_status', $events[0]);
        $this->assertArrayHasKey('status', $events[0]);
        $this->assertArrayHasKey('datetime', $events[0]);
    }

    public function test_api_shipment_not_found_throws_exception()
    {
        $this->expectException(\Xgrz\InPost\Exceptions\ShipXShipmentNotFoundException::class);
        $this->expectExceptionMessage('resource_not_found');
        Http::fake($this->fakeTrackingErrorResponse());
        InPost::trackingInfo('123456789');
    }
}
