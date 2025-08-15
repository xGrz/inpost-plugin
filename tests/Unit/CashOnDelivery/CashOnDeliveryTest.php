<?php

namespace CashOnDelivery;

use Xgrz\InPost\DTOs\CashOnDelivery\CashOnDelivery;
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
        $cod->amount = 1200;

        $this->assertEquals([
            'amount' => 1200,
            'currency' => 'PLN',
        ], $cod->payload());
    }

    public function test_cash_on_delivery_returns_payload_with_amount_and_custom_currency()
    {
        $cod = new CashOnDelivery();
        $cod->amount = 12.45;
        $cod->currency = 'USD';

        $this->assertEquals([
            'amount' => 12.45,
            'currency' => 'USD',
        ], $cod->payload());
    }


    //todo: add toArray test
}
