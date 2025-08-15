<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\DTOs\Address\Receiver;
use Xgrz\InPost\DTOs\Address\Sender;
use Xgrz\InPost\DTOs\Parcels\Parcels;

class InPostShipment
{
    public ?Sender $sender = NULL; // if not provided, will be set default InPost data
    public Receiver $receiver;
    public Parcels $parcels;

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
        $this->parcels = Parcels::make();
    }

    public function payload(): array
    {
        return [
            'sender' => $this->sender?->payload(),
            'receiver' => $this->receiver->payload(),
            'parcels' => $this->parcels->payload(),
        ];
    }
}