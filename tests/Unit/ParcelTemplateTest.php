<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\ParcelTemplate;
use Xgrz\InPost\Tests\InPostTestCase;

class ParcelTemplateTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_can_return_parcel_locker_template_enum()
    {
        $templates = InPost::parcelLockerTemplates();

        $this->assertNotEmpty($templates);
        $this->assertCount(3, $templates);

        $this->assertInstanceOf(\Xgrz\InPost\Enums\ParcelLockerTemplate::class, $templates[0]);
    }

    public function test_can_return_parcel_locker_address_template_enum()
    {
        $templates = InPost::parcelAddressTemplates();
        $this->assertNotEmpty($templates);
        $this->assertCount(4, $templates);
        $this->assertInstanceOf(\Xgrz\InPost\Enums\ParcelLockerTemplate::class, $templates[0]);
    }

    public function test_can_create_custom_parcel_template()
    {
        ParcelTemplate::create(['name' => 'Custom parcel template', 'width' => 10, 'length' => 10, 'height' => 10, 'weight' => 10.2]);
        ParcelTemplate::create(['name' => 'Custom parcel template2', 'width' => 20, 'length' => 15, 'height' => 12, 'weight' => 0.5, 'non_standard' => true]);

        $this->assertDatabaseCount('parcel_templates', 2);
        $this->assertDatabaseHas('parcel_templates', [
            'name' => 'Custom parcel template',
            'width' => 10,
            'length' => 10,
            'height' => 10,
            'weight' => 10.2,
        ]);
        $this->assertDatabaseHas('parcel_templates', [
            'name' => 'Custom parcel template2',
            'width' => 20,
            'length' => 15,
            'height' => 12,
            'weight' => 0.5,
            'non_standard' => true,
        ]);
    }

    public function test_can_fetch_custom_parcel_templates()
    {
        ParcelTemplate::create(['name' => 'Custom parcel template', 'width' => 10, 'length' => 10, 'height' => 10, 'weight' => 10.2]);
        ParcelTemplate::create(['name' => 'Custom parcel template2', 'width' => 20, 'length' => 15, 'height' => 12, 'weight' => 0.5, 'non_standard' => true]);

        $templates = InPost::parcelCourierTemplates();

        $this->assertCount(2, $templates);
        $this->assertEquals('Custom parcel template', $templates[0]['name']);
        $this->assertEquals('Custom parcel template2', $templates[1]['name']);
    }

}
