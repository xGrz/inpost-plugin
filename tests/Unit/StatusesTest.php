<?php

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class StatusesTest extends InPostTestCase
{
    public function test_api_call_to_fetch_statuses()
    {
        Http::fake($this->fakeStatusesResponse());
        InPost::statuses();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/statuses');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_can_get_status_by_name()
    {
        Http::fake($this->fakeStatusesResponse());

        $status = InPost::getStatusDescription('confirmed');
        $status2 = InPost::getStatusDescription('stored_by_sender');

        $this->assertEquals('confirmed', $status['name']);
        $this->assertEquals('stored_by_sender', $status2['name']);

        // This is checking the application is caching statuses
        Http::assertSentCount(1);
    }


}
