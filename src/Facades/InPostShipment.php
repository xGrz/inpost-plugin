<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\DTOs\Address\Receiver;
use Xgrz\InPost\DTOs\Address\Sender;
use Xgrz\InPost\DTOs\CashOnDelivery\CashOnDelivery;
use Xgrz\InPost\DTOs\Insurance\Insurance;
use Xgrz\InPost\DTOs\Parcels\Parcels;
use Xgrz\InPost\DTOs\Services\ShipmentService;

class InPostShipment
{
    public ?Sender $sender = NULL; // if not provided, will be set default InPost data
    public Receiver $receiver;
    public Parcels $parcels;
    public CashOnDelivery $cash_on_delivery;
    public Insurance $insurance;
    public ShipmentService $service;

    public ?string $reference = NULL;
    public bool $is_return = false;
    public ?string $mpk = NULL; // todo: fix this; -> add check if mpk exists
    public ?string $comments = NULL;

    public bool $only_choice_of_offer = false;

    public function __construct()
    {
        $this->receiver = new Receiver();
        $this->parcels = Parcels::make();
        $this->cash_on_delivery = new CashOnDelivery();
        $this->insurance = new Insurance();
        $this->service = ShipmentService::make();
    }

    public function payload(): array
    {
        return [
                'sender' => $this->sender?->payload(),
                'receiver' => $this->receiver->payload(),
                'parcels' => $this->parcels->payload(),
                'cod' => $this->cash_on_delivery->payload(),
                'insurance' => $this->insurance->payload(),
                'only_choice_of_offer' => $this->only_choice_of_offer,
                'reference' => $this->reference,
                'is_return' => $this->is_return,
                'mpk' => $this->mpk,
                'comments' => $this->comments,
            ] + $this->service->payload();
    }
}