<?php

namespace Xgrz\InPost\ShipmentComponents\Address;

use Xgrz\InPost\Enums\AddressType;

class Receiver extends BaseAddress
{
    protected AddressType $type = AddressType::RECEIVER;
}
