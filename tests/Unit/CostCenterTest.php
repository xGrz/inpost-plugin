<?php

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class CostCenterTest extends InPostTestCase
{
    public function test_api_call_to_fetch_costs_center_list()
    {
        Http::fake($this->fakeCostCentersListResponse());
        InPost::costCenters()->get();

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/1916/mpks');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));
    }

    public function test_can_fetch_cost_center_details()
    {
        Http::fake($this->fakeCostCenterDetailsResponse());
        InPost::costCenters()->details(2);

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/mpks/2');
        Http::assertSent(fn($request) => $request->method() === 'GET');
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));;
    }

    public function test_can_create_cost_center()
    {
        Http::fake($this->fakeCostCenterDetailsResponse());
        InPost::costCenters()->create('Cost center name', 'Cost center description');

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/1916/mpks');
        Http::assertSent(fn($request) => $request->method() === 'POST');
        Http::assertSent(fn($request) => $request->data() === [
                'name' => 'Cost center name',
                'description' => 'Cost center description',
            ]);
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));;
    }

    public function test_can_update_cost_center()
    {
        Http::fake($this->fakeCostCenterUpdateResponse());
        InPost::costCenters()->update('Cost center name UPD', 'Cost center description UPD', 3);

        Http::assertSent(fn($request) => $request->url() === 'https://sandbox-api-shipx-pl.easypack24.net/v1/mpks/3');
        Http::assertSent(fn($request) => $request->method() === 'PUT');
        Http::assertSent(fn($request) => $request->data() === [
                'name' => 'Cost center name UPD',
                'description' => 'Cost center description UPD',
            ]);
        Http::assertSent(fn($request) => $request->hasHeader('Authorization', 'Bearer INPOST-TOKEN'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Content-Type', 'application/json'));
        Http::assertSent(fn($request) => $request->hasHeader('Accept-Language', 'pl-PL'));;
    }

}
