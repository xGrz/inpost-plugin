<?php

namespace Xgrz\InPost\Tests\Unit;

use Xgrz\InPost\DTOs\Address\Sender;
use Xgrz\InPost\Tests\InPostTestCase;

class AddressStructTest extends InPostTestCase
{
    public function test_can_divide_name_and_surname()
    {
        $sender = new Sender();
        $sender->name = 'John Kowalski';

        $this->assertEquals('John', $sender->first_name);
        $this->assertEquals('Kowalski', $sender->last_name);
    }

    public function test_can_divide_dual_first_name()
    {
        $sender = new Sender();
        $sender->name = 'John Jerry Kowalski';

        $this->assertEquals('John', $sender->first_name);
        $this->assertEquals('Jerry Kowalski', $sender->last_name);
    }

    public function test_can_divide_dual_last_name()
    {
        $sender = new Sender();
        $sender->name = 'John Doe-Kowalski';

        $this->assertEquals('John', $sender->first_name);
        $this->assertEquals('Doe-Kowalski', $sender->last_name);
    }

    public function test_can_get_joined_name_from_first_and_last_name()
    {
        $sender = new Sender();
        $sender->name = 'John Kowalski';

        $this->assertEquals('John Kowalski', $sender->name);
    }

    public function test_can_get_joined_name_from_dual_first_and_last_name()
    {
        $sender = new Sender();
        $sender->name = 'John Doe-Kowalski';

        $this->assertEquals('John Doe-Kowalski', $sender->name);
    }

    public function test_can_get_joined_name_from_first_and_dual_last_name()
    {
        $sender = new Sender();
        $sender->name = 'John Doe-Kowalski';

        $this->assertEquals('John Doe-Kowalski', $sender->name);
    }

    public function test_pass_only_one_word_to_name()
    {
        $sender = new Sender();
        $sender->name = 'John';

        $this->assertEquals('John', $sender->name);
        $this->assertEquals('John', $sender->first_name);
        $this->assertEquals('', $sender->last_name);
    }

    public function test_can_receive_short_address_payload()
    {
        $sender = new Sender();
        $sender->name = 'John Kowalski';
        $sender->company_name = 'InPost Ltd.';
        $sender->phone = '123456789';
        $sender->email = 'test@example.com';

        $payload = $sender->payload();

        $this->assertEquals('InPost Ltd.', $payload['company_name']);
        $this->assertEquals('John', $payload['first_name']);
        $this->assertEquals('Kowalski', $payload['last_name']);
        $this->assertEquals('123456789', $payload['phone']);
        $this->assertEquals('test@example.com', $payload['email']);
        $this->assertArrayNotHasKey('address', $payload);;
    }

    public function test_can_receive_full_address_payload()
    {
        $sender = new Sender();
        $sender->name = 'John Kowalski';
        $sender->company_name = 'InPost Ltd.';
        $sender->phone = '123456789';
        $sender->email = 'test@example.com';
        $sender->city = 'Warsaw';
        $sender->post_code = '00-000';
        $sender->street = 'Testowa 1';

        $payload = $sender->payload();

        $this->assertArrayHasKey('address', $payload);
        $this->assertEquals('Warsaw', $payload['address']['city']);
        $this->assertEquals('00-000', $payload['address']['post_code']);
        $this->assertEquals('Testowa 1', $payload['address']['street']);
        $this->assertEquals('PL', $payload['address']['country_code']);
    }

    public function test_payload_post_code_is_formatted()
    {
        $sender = new Sender();
        $sender->post_code = '12345';

        $payload = $sender->payload();
        $this->assertEquals('12-345', $payload['address']['post_code']);
    }

    public function test_empty_address_return_null()
    {
        $sender = new Sender();
        $this->assertNull($sender->payload());;
    }

}