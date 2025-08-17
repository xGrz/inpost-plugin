<?php

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class ServicesTest extends InPostTestCase
{

    public function test_api_call_to_fetch_services()
    {
        Http::fake($this->fakeServicesResponse());
        InPost::services();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/services');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_can_get_service_by_identifier()
    {
        Http::fake($this->fakeServicesResponse());

        $s1 = InPost::getServiceDescription('inpost_courier_express_1700');
        $s2 = InPost::getServiceDescription('inpost_locker_standard');
        $s3 = InPost::getServiceDescription('inpost_courier_standard');

        $this->assertEquals('inpost_courier_express_1700', $s1['id']);
        $this->assertEquals('inpost_locker_standard', $s2['id']);
        $this->assertEquals('inpost_courier_standard', $s3['id']);

    }


}
