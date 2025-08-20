<?php

namespace Xgrz\InPost\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Xgrz\InPost\DTOs\Parcels\CustomParcel;
use Xgrz\InPost\DTOs\Parcels\LockerParcel;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Facades\InPostShipment;
use Xgrz\InPost\Jobs\UpdateInPostServicesJob;
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
        $this->costsCenter = InPost::costCenters()->get()['items'][1]; // todo: this should be fixed
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
            ->additionalService('SMS')
            ->additionalService('Email');

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

    public function testCanAssignParcelsForCourier()
    {
        $s = new InPostShipment();
        $s->parcels->add(CustomParcel::make(40, 20, 10, 5, 1, false));

        $this->assertCount(1, $s->payload()['parcels']);
        $this->assertSame(400, $s->payload()['parcels'][0]['dimensions']['width']);
        $this->assertSame(200, $s->payload()['parcels'][0]['dimensions']['height']);
        $this->assertSame(100, $s->payload()['parcels'][0]['dimensions']['length']);
        $this->assertSame(5, $s->payload()['parcels'][0]['dimensions']['weight']);
        $this->assertFalse($s->payload()['parcels'][0]['non_standard']);
    }

    public function testCanAssignInsurance()
    {
        $s = new InPostShipment();
        $s->insurance->amount = 2000;

        $this->assertSame(2000, $s->payload()['insurance']['amount']);
        $this->assertSame('PLN', $s->payload()['insurance']['currency']);
    }

    public function testCanAssignCashOnDelivery()
    {
        $s = new InPostShipment();
        $s->cash_on_delivery->amount = 100;

        $this->assertSame(100, $s->payload()['cod']['amount']);
        $this->assertSame('PLN', $s->payload()['cod']['currency']);
    }

    public function testCostSaverForInsuranceWhenLowValue()
    {
        $s = new InPostShipment();
        $s->insurance->amount = 300;

        $this->assertNull($s->payload()['insurance']);
    }

    public function testInsuranceIsRequiredWhenCachOnDeliveryIsGiven()
    {
        $s = new InPostShipment();
        $s->cash_on_delivery->amount = 100;

        $this->assertArrayHasKey('insurance', $s->payload());
        $this->assertSame(100, $s->payload()['insurance']['amount']);

        $this->assertArrayHasKey('cod', $s->payload());;
        $this->assertSame(100, $s->payload()['cod']['amount']);
    }

    public function testCanAssignReference()
    {
        $s = new InPostShipment();
        $s->reference = 'Order #5000';

        $this->assertSame('Order #5000', $s->payload()['reference']);
    }

    public function testCanAssignComments()
    {
        $s = new InPostShipment();
        $s->comments = 'This is a test shipment';

        $this->assertSame('This is a test shipment', $s->payload()['comments']);
    }

    public function testAssignCostCenter()
    {
        $s = new InPostShipment();
        $s->mpk = $this->costsCenter['name'];

        $this->assertSame('Second', $s->payload()['mpk']);
    }

    public function testCannotAssignUnknownCostCenter()
    {
        $s = new InPostShipment();
        $s->mpk = 'Not existing Cost Center';

        $this->assertNull($s->payload()['mpk']);
    }

    public function testShipmentToArray()
    {
    }

}