<?php

namespace Components;

use Xgrz\InPost\ShipmentComponents\CashOnDelivery\CashOnDelivery;
use Xgrz\InPost\Tests\InPostTestCase;

class CashOnDeliveryTest extends InPostTestCase
{
    public function test_empty_cash_on_delivery_returns_null()
    {
        $cod = new CashOnDelivery();

        $this->assertNull($cod->payload());
    }

    public function test_cash_on_delivery_returns_payload_with_amount_and_currency()
    {
        $cod = new CashOnDelivery();
        $cod->set(1200);

        $this->assertEquals([
            'amount' => 1200,
            'currency' => 'PLN',
        ], $cod->payload());
    }

    public function test_cash_on_delivery_returns_payload_with_amount_and_custom_currency()
    {
        $cod = new CashOnDelivery();
        $cod->set(12.45, 'USD');

        $this->assertEquals([
            'amount' => 12.45,
            'currency' => 'USD',
        ], $cod->payload());
    }


    public function test_cod_return_array_of_values()
    {
        $cod = new CashOnDelivery();
        $cod->set(12.45, 'USD');

        $this->assertIsArray($cod->toArray());

        $this->assertArrayHasKey('cod', $cod->toArray());
        $this->assertEquals(12.45, $cod->toArray()['cod']);

        $this->assertArrayHasKey('cod_currency', $cod->toArray());
        $this->assertEquals('USD', $cod->toArray()['cod_currency']);
    }

    public function test_cod_return_array_with_null_values_on_empty()
    {
        $cod = new CashOnDelivery();
        $this->assertIsArray($cod->toArray());
        $this->assertArrayHasKey('cod', $cod->toArray());
        $this->assertArrayHasKey('cod_currency', $cod->toArray());
        $this->assertNull($cod->toArray()['cod']);
        $this->assertNull($cod->toArray()['cod_currency']);
    }

}
