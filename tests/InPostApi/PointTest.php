<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class PointTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_api_call_to_fetch_points_with_name_search()
    {
        Http::fake($this->fakePointResponse());
        $response = InPost::point('WAW365');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/points?name=WAW365');;
        $this->assertIsObject($response);
    }

}
