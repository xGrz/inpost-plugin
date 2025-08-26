<?php

namespace Xgrz\InPost\Tests\Parcels\Items;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xgrz\InPost\DTOs\ParcelTemplateDTO;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\ParcelTemplate;
use Xgrz\InPost\Tests\InPostTestCase;

class ParcelTemplateTest extends InPostTestCase
{
    use RefreshDatabase;

    public function test_can_return_parcel_locker_templates()
    {
        $templates = InPost::parcelLockerTemplates();

        $this->assertNotEmpty($templates);
        $this->assertCount(3, $templates);

        $this->assertInstanceOf(ParcelTemplateDTO::class, $templates[0]);
    }

    public function test_can_return_parcel_locker_to_address_templates()
    {
        $templates = InPost::parcelAddressTemplates();
        $this->assertNotEmpty($templates);
        $this->assertCount(4, $templates);
        $this->assertInstanceOf(ParcelTemplateDTO::class, $templates[0]);
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
        $this->assertEquals('Custom parcel template', $templates[0]->name);
        $this->assertEquals('Custom parcel template2', $templates[1]->name);
    }

    public function test_can_create_parcel_template_dto_form_enum()
    {
        $parcel = ParcelTemplateDTO::make(ParcelLockerTemplate::S);

        $this->assertEquals('Paczkomat A (8x64x38 25kg)', $parcel->label);
        $this->assertEquals(ParcelLockerTemplate::S->getLabel(), $parcel->name);
        $this->assertEquals(ParcelLockerTemplate::S->getWidth(), $parcel->width);
        $this->assertEquals(ParcelLockerTemplate::S->getHeight(), $parcel->height);
        $this->assertEquals(ParcelLockerTemplate::S->getLength(), $parcel->length);
        $this->assertEquals(ParcelLockerTemplate::S->getMaxWeight(), $parcel->weight);
        $this->assertFalse($parcel->non_standard);
    }

    public function test_can_create_parcel_template_dto_form_model()
    {
        ParcelTemplate::create(['name' => 'Custom parcel template2', 'width' => 20, 'length' => 15, 'height' => 12, 'weight' => 0.5, 'non_standard' => true]);
        $parcel = ParcelTemplateDTO::make(ParcelTemplate::latest()->first());

        $this->assertEquals('Custom parcel template2 (20x12x15 0.5kg)', $parcel->label);
        $this->assertEquals('Custom parcel template2', $parcel->name);
        $this->assertEquals(20, $parcel->width);
        $this->assertEquals(12, $parcel->height);
        $this->assertEquals(15, $parcel->length);
        $this->assertEquals(0.5, $parcel->weight);
        $this->assertTrue($parcel->non_standard);
    }

}
