<?php

namespace Xgrz\InPost\ShipmentComponents\Address;

use Xgrz\InPost\Enums\AddressType;

class Sender extends BaseAddress
{
    protected AddressType $type = AddressType::SENDER;
}
