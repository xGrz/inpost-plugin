<?php

use Xgrz\InPost\Tests\InPostTestCase;

class WebhookConsumeTest extends InPostTestCase
{
    public function test_example()
    {
        dd($this->fakeRequestPayload('ShipmentConfirmed.json'));
    }
}