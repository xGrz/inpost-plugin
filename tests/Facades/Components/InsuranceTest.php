<?php

namespace Components;

use Xgrz\InPost\ShipmentComponents\Insurance\Insurance;
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
        $insurance->set(1200);

        $this->assertEquals([
            'amount' => 1200,
            'currency' => 'PLN',
        ], $insurance->payload());
    }

    public function test_insurance_returns_payload_with_amount_and_custom_currency()
    {
        $insurance = new Insurance();
        $insurance->set(12.45, 'USD');

        $this->assertEquals([
            'amount' => 12.45,
            'currency' => 'USD',
        ], $insurance->payload());
    }

    public function test_insurance_return_array_of_values()
    {
        $insurance = new Insurance();
        $insurance->set(12.45, 'USD');

        $this->assertIsArray($insurance->toArray());
        $this->assertArrayHasKey('insurance', $insurance->toArray());
        $this->assertArrayHasKey('insurance_currency', $insurance->toArray());
        $this->assertEquals(12.45, $insurance->toArray()['insurance']);
        $this->assertEquals('USD', $insurance->toArray()['insurance_currency']);
    }

    public function test_insurance_return_array_with_null_values_on_empty()
    {
        $insurance = new Insurance();
        $this->assertIsArray($insurance->toArray());
        $this->assertArrayHasKey('insurance', $insurance->toArray());
        $this->assertArrayHasKey('insurance_currency', $insurance->toArray());
        $this->assertNull($insurance->toArray()['insurance']);
        $this->assertNull($insurance->toArray()['insurance_currency']);
    }
}
