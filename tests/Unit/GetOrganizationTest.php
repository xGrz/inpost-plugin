<?php

namespace Xgrz\InPost\Tests\Unit;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class GetOrganizationTest extends InPostTestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        config([
            'inpost.organization' => '1916',
            'inpost.token' => '123456789',
            'inpost.url' => 'https://sandbox-api-shipx-pl.easypack24.net',
        ]);
    }

    public function test_api_call_to_fetch_organization()
    {
        Http::fake($this->organizationsResponse());
        $result = InPost::organization();

        Http::assertSent(function($request) {
            return $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/1916';
        });

        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer 123456789'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));;
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));;
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));;
        Http::assertSent(fn($request) => $request->method() === 'GET');;
    }
}
