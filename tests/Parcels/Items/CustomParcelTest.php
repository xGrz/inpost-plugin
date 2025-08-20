<?php

namespace Xgrz\InPost\Tests\Parcels\Items;

use Xgrz\InPost\ShipmentComponents\Parcels\CourierParcel;
use Xgrz\InPost\Tests\InPostTestCase;

class CustomParcelTest extends InPostTestCase
{

    public function test_parcel_locker_returns_array_of_values()
    {
        $parcel = (CourierParcel::make(38, 64, 8, 25, 2, true))->toArray();

        $this->assertEquals(38, $parcel['width']);
        $this->assertEquals(64, $parcel['height']);
        $this->assertEquals(8, $parcel['length']);
        $this->assertEquals(25, $parcel['weight']);
        $this->assertEquals(2, $parcel['quantity']);
        $this->assertTrue($parcel['non_standard']);
    }

    public function test_parcel_locker_returns_payload()
    {
        $parcel = (CourierParcel::make(38, 64, 8, 25, 2, true))->payload();

        $this->assertIsArray($parcel);
        $this->assertIsArray($parcel['dimensions']);
        $this->assertEquals(380, $parcel['dimensions']['width']);
        $this->assertEquals(640, $parcel['dimensions']['height']);
        $this->assertEquals(80, $parcel['dimensions']['length']);
        $this->assertEquals('mm', $parcel['dimensions']['unit']);
        $this->assertIsArray($parcel['weight']);
        $this->assertEquals(25, $parcel['weight']['amount']);
        $this->assertEquals('kg', $parcel['weight']['unit']);
        $this->assertTrue($parcel['non_standard']);
    }
}