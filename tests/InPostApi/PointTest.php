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
        $response = InPost::point('KRA02APP');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/points?name=KRA02APP&fields=name%2Cdisplay_name%2Cstatus%2Caddress%2Clocation_247');

        Http::assertSent(fn($request) => dump($request->url()));
        //dd($response);
        $this->assertIsObject($response);
    }

    public function test_point_info() {
        $point = InPost::pointInfo('KRA02APP');
        $this->assertIsArray($point);
    }
}
