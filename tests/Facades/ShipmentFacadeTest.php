<?php

namespace Xgrz\InPost\Tests\Facades;

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Exceptions\ShipXException;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Facades\InPostShipment;
use Xgrz\InPost\Jobs\UpdateInPostServicesJob;
use Xgrz\InPost\ShipmentComponents\Parcels\CourierParcel;
use Xgrz\InPost\ShipmentComponents\Parcels\LockerParcel;
use Xgrz\InPost\Tests\InPostTestCase;

class ShipmentFacadeTest extends InPostTestCase
{
    protected array $costCenter;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'https://sandbox-api-shipx-pl.easypack24.net/v1/services' => $this->fromFile('ServicesResponse.json'),
            'https://sandbox-api-shipx-pl.easypack24.net/v1/organizations/*' => $this->fromFile('CostCentersListResponse.json'),
        ]);
        (new UpdateInPostServicesJob)->handle();
        $this->costsCenter = InPost::costCenters()->get()[1];
    }

    public function testCanAssignReceiverForParcelLockerWithEmailAndPhoneOnly()
    {
        $s = new InPostShipment();
        $s->receiver->email = 'inpost@example.com';
        $s->receiver->phone = '600 123 456';

        $this->assertIsArray($s->payload());

        $this->assertArrayHasKey('email', $s->payload()['receiver']);
        $this->assertSame('inpost@example.com', $s->payload()['receiver']['email']);

        $this->assertArrayHasKey('phone', $s->payload()['receiver']);
        $this->assertSame('600123456', $s->payload()['receiver']['phone']);

        $this->assertCount(2, $s->payload()['receiver'], 'Payload array should expose only email and phone');
    }

    public function testCanAssignReceiverForCourierWithFullAddress()
    {
        $s = new InPostShipment();
        $s->receiver->company_name = 'InPost Sp. z o.o.';;
        $s->receiver->name = 'John Doe';
        $s->receiver->email = 'inpost@example.com';
        $s->receiver->phone = '600 123 456';
        $s->receiver->street = 'Testowa';
        $s->receiver->building_number = '1A';
        $s->receiver->city = 'Warsaw';
        $s->receiver->post_code = '00-001';

        $receiver = $s->payload()['receiver'];

        $this->assertSame('InPost Sp. z o.o.', $receiver['company_name']);

        $this->assertSame('John', $receiver['first_name']);

        $this->assertSame('Doe', $receiver['last_name']);

        $this->assertIsArray($receiver['address']);

        $this->assertSame('Testowa', $receiver['address']['street']);
        $this->assertSame('1A', $receiver['address']['building_number']);
        $this->assertSame('Warsaw', $receiver['address']['city']);
        $this->assertSame('00-001', $receiver['address']['post_code']);

        $this->assertCount(6, $receiver);
        $this->assertCount(5, $receiver['address']);;
    }

    public function testCanAssignService()
    {
        $s = new InPostShipment();
        $s->service->setService('inpost_courier_standard');

        $this->assertSame('inpost_courier_standard', $s->payload()['service']);
    }

    public function testCanAssignAdditionalServiceInLowercase()
    {
        $s = new InPostShipment();
        $s->service
            ->setService('inpost_courier_standard')
            ->additionalServices('SMS')
            ->additionalServices('Email');

        $this->assertSame('inpost_courier_standard', $s->payload()['service']);
        $this->assertArrayHasKey('additional_services', $s->payload());
        $this->assertCount(2, $s->payload()['additional_services']);
        $this->assertTrue(in_array('sms', $s->payload()['additional_services']));
        $this->assertTrue(in_array('email', $s->payload()['additional_services']));;
    }

    public function testCanAssignParcelLocker()
    {
        $s = new InPostShipment();
        $s->parcels->add(LockerParcel::make(ParcelLockerTemplate::S));

        $this->assertCount(1, $s->payload()['parcels']);
        $this->assertSame(ParcelLockerTemplate::S->value, $s->payload()['parcels']['template']);
    }

    public function testCanAssignParcelLockerFromEnumTemplate()
    {
        $s = new InPostShipment();
        $s->parcels->add(ParcelLockerTemplate::M);

        $this->assertCount(1, $s->payload()['parcels']);
        $this->assertSame(ParcelLockerTemplate::M->value, $s->payload()['parcels']['template']);
    }

    public function testCanAssignParcelsForCourier()
    {
        $s = new InPostShipment();
        $s->parcels->add(CourierParcel::make(40, 20, 10, 5, 1, false));

        $this->assertCount(1, $s->payload()['parcels']);
        $this->assertSame(400, $s->payload()['parcels'][0]['dimensions']['width']);
        $this->assertSame(200, $s->payload()['parcels'][0]['dimensions']['height']);
        $this->assertSame(100, $s->payload()['parcels'][0]['dimensions']['length']);
        $this->assertSame('mm', $s->payload()['parcels'][0]['dimensions']['unit']);
        $this->assertEquals(5, $s->payload()['parcels'][0]['weight']['amount']);
        $this->assertSame('kg', $s->payload()['parcels'][0]['weight']['unit']);
        $this->assertFalse($s->payload()['parcels'][0]['non_standard']);
    }

    public function testCanAssignInsurance()
    {
        $s = new InPostShipment();
        $s->insurance->set(2000);

        $this->assertEquals(2000, $s->payload()['insurance']['amount']);
        $this->assertSame('PLN', $s->payload()['insurance']['currency']);
    }

    public function testCanAssignCashOnDelivery()
    {
        $s = new InPostShipment();
        $s->cash_on_delivery->set(100);

        $this->assertEquals(100, $s->payload()['cod']['amount']);
        $this->assertSame('PLN', $s->payload()['cod']['currency']);
    }

    public function testCostSaverForInsuranceWhenLowValue()
    {
        $s = new InPostShipment();
        $s->insurance->set(300);

        $this->assertNull($s->payload()['insurance']);
    }

    public function testInsuranceIsRequiredWhenCachOnDeliveryIsGiven()
    {
        $s = new InPostShipment();
        $s->cash_on_delivery->set(100);

        $this->assertArrayHasKey('insurance', $s->payload());
        $this->assertEquals(100, $s->payload()['insurance']['amount']);

        $this->assertArrayHasKey('cod', $s->payload());;
        $this->assertEquals(100, $s->payload()['cod']['amount']);
    }

    public function testCanAssignReference()
    {
        $s = new InPostShipment();
        $s->reference('Order #5000');

        $this->assertSame('Order #5000', $s->payload()['reference']);
    }

    public function testCanAssignComments()
    {
        $s = new InPostShipment();
        $s->comment('This is a test shipment');

        $this->assertSame('This is a test shipment', $s->payload()['comments']);
    }

    public function testAssignCostCenter()
    {
        $s = new InPostShipment();
        $s->costCenter($this->costsCenter['name']);

        $this->assertSame('Second', $s->payload()['mpk']);
    }

    public function testAssignUnknownCostCenterThrowsException()
    {
        $this->expectException(ShipXException::class);
        $this->expectExceptionMessage(
            'Cost Center with name [Non existing Cost Center] not found.'
        );

        $s = new InPostShipment();
        $s->costCenter('Non existing Cost Center');
    }

    public function testSetReturn()
    {
        $s = new InPostShipment();
        $s->setReturn();
        $this->assertTrue($s->payload()['is_return']);
    }

    public function testOnlyChoiceOfOffer()
    {
        $s = new InPostShipment();
        $s->onlyChoiceOfOffer();
        $this->assertTrue($s->payload()['only_choice_of_offer']);
    }

    public function testShipmentToArray()
    {
        $senderData = [
            'company_name' => 'Funny Company',
            'first_name' => 'Jeremy',
            'last_name' => 'Clarkson',
            'phone' => '700700700',
            'email' => 'jc@example.com',
            'street' => 'Aleja Testowa',
            'building_number' => '11B',
            'city' => 'Warsaw',
            'post_code' => '01001',
        ];
        $receiverData = [
            'company_name' => 'InPost Sp. z o.o.',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'inpost@example.com',
            'phone' => '600123456',
            'street' => 'Testowa',
            'building_number' => '1A',
            'city' => 'Warsaw',
            'post_code' => '00001',
        ];

        $s = new InPostShipment();
        foreach ($senderData as $key => $value) {
            $s->sender->$key = $value;
        }
        foreach ($receiverData as $key => $value) {
            $s->receiver->$key = $value;
        }

        $s->parcels->add(CourierParcel::make(40, 20, 10, 5, 2, true));
        $s->insurance->set(600);
        $s->cash_on_delivery->set(1100);
        $s->service
            ->setService('inpost_courier_standard')
            ->additionalServices(['SMS', 'Email'])
            ->targetPoint('WAW375A');

        $s->reference('Order #5000');
        $s->comment('This is a test shipment');
        $s->costCenter($this->costsCenter['name']);
        $s->setReturn();
        $s->onlyChoiceOfOffer();

        $arr = $s->toArray();

        $this->assertIsArray($arr);
        $this->assertEquals($senderData + ['country_code' => 'PL'], $arr['sender']);
        $this->assertEquals($receiverData + ['country_code' => 'PL'], $arr['receiver']);

        $this->assertCount(1, $arr['parcels']);
        $this->assertEquals(40, $arr['parcels'][0]['width']);
        $this->assertEquals(20, $arr['parcels'][0]['height']);
        $this->assertEquals(10, $arr['parcels'][0]['length']);
        $this->assertEquals(5, $arr['parcels'][0]['weight']);
        $this->assertSame(2, $arr['parcels'][0]['quantity']);;
        $this->assertTrue($arr['parcels'][0]['non_standard']);

        $this->assertEquals('inpost_courier_standard', $arr['service']);
        $this->assertEquals(['sms', 'email'], $arr['additional_services']);
        $this->assertEquals('WAW375A', $arr['target_point']);
        $this->assertEquals('Order #5000', $arr['reference']);
        $this->assertEquals('This is a test shipment', $arr['comment']);
        $this->assertEquals('Second', $arr['cost_center']);
        $this->assertTrue($arr['is_return']);
        $this->assertTrue($arr['only_choice_of_offer']);
        $this->assertEquals(600, $arr['insurance']);
        $this->assertEquals('PLN', $arr['insurance_currency']);
        $this->assertEquals(1100, $arr['cod']);
        $this->assertEquals('PLN', $arr['cod_currency']);
    }

    public function test_use_target_point_helper_for_setting_parcel_locker_shipment()
    {
        $s = new InPostShipment();
        $s->targetPoint('WAW375A', 'test@example.com', '500777535', 'inpost_locker_standard');

        $this->assertArrayHasKey('custom_attributes', $s->payload());
        $this->assertArrayHasKey('target_point', $s->payload()['custom_attributes']);
        $this->assertArrayHasKey('receiver', $s->payload());
        $this->assertEquals('test@example.com', $s->payload()['receiver']['email']);;
        $this->assertSame('500777535', $s->payload()['receiver']['phone']);
        $this->assertEquals('inpost_locker_standard', $s->payload()['service']);

        $this->assertEquals('WAW375A', $s->payload()['custom_attributes']['target_point']);

    }
}