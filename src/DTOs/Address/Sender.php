<?php

namespace Xgrz\InPost\DTOs\Address;


use Xgrz\InPost\Enums\AddressType;

class Sender extends BaseAddress
{
    protected AddressType $type = AddressType::SENDER;
}
