<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\DTOs\Address\Receiver;
use Xgrz\InPost\DTOs\Address\Sender;

class InPostShipment
{
    public ?Sender $sender = NULL; // if not provided, will be set default InPost data
    public Receiver $receiver;

    // parcels

    // custom_attributes

    // cod

    // insurance

    // reference

    // is_return

    // service

    // additional_services

    // mpk

    // comments

    public function __construct()
    {
        $this->receiver = new Receiver();
    }
}