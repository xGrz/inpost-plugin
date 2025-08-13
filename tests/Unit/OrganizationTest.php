<?php

namespace Xgrz\InPost\Tests\Unit;

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class OrganizationTest extends InPostTestCase
{

    public function test_api_call_to_fetch_organization()
    {
        Http::fake($this->fakeOrganizationsResponse());
        InPost::organization();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/1916');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }
}
