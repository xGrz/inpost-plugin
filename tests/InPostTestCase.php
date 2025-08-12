<?php

namespace Xgrz\InPost\Tests;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;

abstract class InPostTestCase extends TestCase
{

    public function organizationsResponse()
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
