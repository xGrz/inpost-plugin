<?php

namespace Xgrz\InPost\Tests\Parcels\Items;

use Xgrz\InPost\DTOs\Parcels\LockerParcel;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Tests\InPostTestCase;

class ParcelLockerTest extends InPostTestCase
{

    public function test_parcel_locker_returns_array_of_values()
    {
        $parcel = (LockerParcel::make('small'))->toArray();

        $this->assertEquals(38, $parcel['width']);
        $this->assertEquals(64, $parcel['height']);
        $this->assertEquals(8, $parcel['length']);
        $this->assertEquals(25, $parcel['weight']);
        $this->assertEquals(1, $parcel['quantity']);
        $this->assertFalse($parcel['non_standard']);
    }

    public function test_parcel_locker_returns_payload()
    {
        $parcel = (LockerParcel::make(ParcelLockerTemplate::XL))->payload();

        $this->assertIsArray($parcel);
        $this->assertArrayHasKey('template', $parcel);
        $this->assertSame('xlarge', $parcel['template']);;
    }
}