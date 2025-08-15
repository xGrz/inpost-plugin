<?php

namespace Xgrz\InPost\Tests\Feature;

use Xgrz\InPost\DTOs\Parcels\CustomParcel;
use Xgrz\InPost\DTOs\Parcels\LockerParcel;
use Xgrz\InPost\DTOs\Parcels\Parcels;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Tests\InPostTestCase;

class ParcelsTest extends InPostTestCase
{
    public function test_can_add_parcel_locker_to_parcels()
    {
        $parcels = new Parcels();
        $parcels->add(LockerParcel::make(ParcelLockerTemplate::M));

        $payload = $parcels->payload();

        $this->assertIsArray($payload);
        $this->assertEquals(ParcelLockerTemplate::M->value, $payload['template']);
    }

    public function test_can_add_custom_parcel_to_parcels()
    {
        $parcels = new Parcels();
        $parcels->add(CustomParcel::make(38, 64, 8, 25, 2, true));
        $parcels->add(LockerParcel::make(ParcelLockerTemplate::M));

        $payload = $parcels->payload();

        $this->assertIsArray($payload);
        $this->assertCount(3, $payload);
    }

    public function test_can_get_proper_parcel_payload_for_parcel_locker()
    {
        $parcels = new Parcels();
        $parcels->add(LockerParcel::make(ParcelLockerTemplate::S));

        $payload = $parcels->payload();

        $this->assertIsArray($payload);
        $this->assertArrayHasKey('template', $payload);
        $this->assertEquals(ParcelLockerTemplate::S->value, $payload['template']);

    }

    public function test_can_get_proper_parcel_payload_for_courier()
    {
        $parcels = new Parcels();
        $parcels->add(CustomParcel::make(38, 64, 8, 25, 2, true));
        $parcels->add(LockerParcel::make(ParcelLockerTemplate::M));

        $payload = $parcels->payload();

        $this->assertArrayHasKey('id', $payload[0]);
        $this->assertArrayHasKey('dimensions', $payload[0]);
        $this->assertEquals(380, $payload[0]['dimensions']['width']);
        $this->assertEquals(640, $payload[0]['dimensions']['height']);
        $this->assertEquals(80, $payload[0]['dimensions']['length']);
        $this->assertEquals('mm', $payload[0]['dimensions']['unit']);

        $this->assertArrayHasKey('weight', $payload[0]);
        $this->assertEquals(25, $payload[0]['weight']['amount']);;
        $this->assertEquals('kg', $payload[0]['weight']['unit']);;
        $this->assertTrue($payload[0]['non_standard']);

        $this->assertArrayHasKey('id', $payload[1]);
        $this->assertArrayHasKey('dimensions', $payload[1]);
        $this->assertEquals(380, $payload[1]['dimensions']['width']);
        $this->assertEquals(640, $payload[1]['dimensions']['height']);
        $this->assertEquals(80, $payload[1]['dimensions']['length']);
        $this->assertEquals('mm', $payload[1]['dimensions']['unit']);

        $this->assertArrayHasKey('weight', $payload[1]);
        $this->assertEquals(25, $payload[1]['weight']['amount']);;
        $this->assertEquals('kg', $payload[1]['weight']['unit']);;
        $this->assertTrue($payload[1]['non_standard']);

        $this->assertArrayHasKey('id', $payload[2]);
        $this->assertArrayHasKey('template', $payload[2]);
        $this->assertEquals(ParcelLockerTemplate::M->value, $payload[2]['template']);;

    }
}