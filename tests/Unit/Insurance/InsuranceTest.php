<?php

namespace Insurance;

use Xgrz\InPost\DTOs\Insurance\Insurance;
use Xgrz\InPost\Tests\InPostTestCase;

class InsuranceTest extends InPostTestCase
{
    public function test_empty_insurance_returns_null()
    {
        $insurance = new Insurance();

        $this->assertNull($insurance->payload());
    }

    public function test_insurance_returns_payload_with_amount_and_currency()
    {
        $insurance = new Insurance();
        $insurance->amount = 1200;

        $this->assertEquals([
            'amount' => 1200,
            'currency' => 'PLN',
        ], $insurance->payload());
    }

    public function test_insurance_returns_payload_with_amount_and_custom_currency()
    {
        $insurance = new Insurance();
        $insurance->amount = 12.45;
        $insurance->currency = 'USD';

        $this->assertEquals([
            'amount' => 12.45,
            'currency' => 'USD',
        ], $insurance->payload());
    }

    //todo: add toArray test
}
