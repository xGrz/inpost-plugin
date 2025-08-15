<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\DTOs\Address\Receiver;
use Xgrz\InPost\DTOs\Address\Sender;
use Xgrz\InPost\DTOs\CashOnDelivery\CashOnDelivery;
use Xgrz\InPost\DTOs\Insurance\Insurance;
use Xgrz\InPost\DTOs\Parcels\Parcels;

class InPostShipment
{
    public ?Sender $sender = NULL; // if not provided, will be set default InPost data
    public Receiver $receiver;
    public Parcels $parcels;

    // custom_attributes

    public CashOnDelivery $cash_on_delivery;

    public Insurance $insurance;

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
        $this->cash_on_delivery = new CashOnDelivery();
        $this->insurance = new Insurance();
    }

    public function payload(): array
    {
        return [
            'sender' => $this->sender?->payload(),
            'receiver' => $this->receiver->payload(),
            'parcels' => $this->parcels->payload(),
            'cod' => $this->cash_on_delivery->payload(),
            'insurance' => $this->insurance->payload(),
        ];
    }
}