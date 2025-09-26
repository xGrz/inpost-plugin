<?php

namespace Xgrz\InPost\Tests\InPostApi;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class ServicesTest extends InPostTestCase
{
    use RefreshDatabase;

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

    public function test_can_get_services()
    {
        Http::fake($this->fakeServicesResponse());
        $services = InPost::services();

        $this->assertInstanceOf(Collection::class, $services);
        $this->assertTrue($services->has('inpost_courier_standard'));
    }

}
