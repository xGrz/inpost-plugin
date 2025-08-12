<?php

namespace Xgrz\InPost\Tests;

use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase;
use Xgrz\InPost\InPostServiceProvider;

abstract class InPostTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('inpost.organization', '1916');
        $app['config']->set('inpost.token', 'INPOST-TOKEN');
        $app['config']->set('inpost.url', 'https://sandbox-api-shipx-pl.easypack24.net');
        $app['config']->set('inpost.widget', 'WIDGET-TOKEN');
        $app['config']->set('inpost.minimum_insurance_value', 500);
        $app['config']->set('inpost.synchronize_points_chunk_size', 500);
    }

    protected function getPackageProviders($app): array
    {
        return [
            InPostServiceProvider::class,
        ];
    }

    public function organizationsResponse(): array
    {
        return [
            '*' => Http::response(
                [
                    "href" => "https://api.shipx.pl.easypack24.net/v1/organizations/1916",
                    "id" => 1916,
                    "owner_id" => 1,
                    "tax_id" => "3973902075",
                    "name" => "Random org name39739020755741",
                    "created_at" => "2016-10-04T10:36:49.631+02:00",
                    "updated_at" => "2016-10-04T10:36:49.631+02:00",
                    "services" => [
                        "inpost_locker_standard",
                        "inpost_courier_standard",
                    ],
                    "address" => [
                        "id" => 808,
                        "line1" => NULL,
                        "line2" => NULL,
                        "street" => "Ulica jakaś39739020755741",
                        "building_number" => "Budynek39739020755741",
                        "city" => "Szczecin39739020755741",
                        "post_code" => "22-100",
                        "country_code" => "PL",
                    ],
                ],
                200
            ),
        ];
    }
}
