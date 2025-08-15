<?php

namespace Xgrz\InPost\DTOs\Address;

use Xgrz\InPost\Enums\AddressType;

class Receiver extends BaseAddress
{
    protected AddressType $type = AddressType::RECEIVER;
}
