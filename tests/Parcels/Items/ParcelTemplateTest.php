<?php

namespace Xgrz\InPost\Tests\Parcels\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xgrz\InPost\Enums\InPostParcelLocker;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Tests\InPostTestCase;

class ParcelTemplateTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_can_return_parcel_locker_templates()
    {
        $templates = InPost::parcelLockerTemplates();

        $this->assertNotEmpty($templates);
        $this->assertCount(3, $templates);

        $this->assertInstanceOf(InPostParcelLocker::class, $templates[0]);
    }

    public function test_can_return_parcel_locker_to_address_templates()
    {
        $templates = InPost::parcelAddressTemplates();
        $this->assertNotEmpty($templates);
        $this->assertCount(4, $templates);
        $this->assertInstanceOf(InPostParcelLocker::class, $templates[0]);
    }

}
