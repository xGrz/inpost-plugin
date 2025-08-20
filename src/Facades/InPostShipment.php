<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\DTOs\Address\Receiver;
use Xgrz\InPost\DTOs\Address\Sender;
use Xgrz\InPost\DTOs\CashOnDelivery\CashOnDelivery;
use Xgrz\InPost\DTOs\Insurance\Insurance;
use Xgrz\InPost\DTOs\Parcels\Parcels;
use Xgrz\InPost\DTOs\Services\ShipmentService;
use Xgrz\InPost\Exceptions\ShipXException;

class InPostShipment
{
    public ?Sender $sender = NULL; // if not provided, will be set default InPost data
    public readonly Receiver $receiver;
    public readonly Parcels $parcels;
    public readonly CashOnDelivery $cash_on_delivery;
    public readonly Insurance $insurance;
    public readonly ShipmentService $service;
    private ?string $reference = NULL;
    private bool $is_return = false;
    private ?string $mpk = NULL;
    private ?string $comments = NULL;
    private bool $only_choice_of_offer = false;

    public function __construct()
    {
        $this->receiver = new Receiver();
        $this->parcels = Parcels::make();
        $this->cash_on_delivery = new CashOnDelivery();
        $this->insurance = new Insurance();
        $this->service = ShipmentService::make();
    }

    public function comment(string $comment): static
    {
        $this->comments = $comment;
        return $this;
    }

    public function reference(string $reference): static
    {
        $this->reference = $reference;
        return $this;
    }


    public function setReturn(bool $isReturn = true): static
    {
        $this->is_return = $isReturn;
        return $this;
    }

    public function onlyChoiceOfOffer(bool $onlyChoiceOfOffer = true): static
    {
        $this->only_choice_of_offer = $onlyChoiceOfOffer;
        return $this;
    }

    /**
     * @throws ShipXException
     */
    public function costCenter(string $costCenterName): static
    {
        $exists = InPost::costCenters()
            ->get()
            ->filter(fn($costCenter) => $costCenter['name'] === $costCenterName)
            ->first();

        if (!$exists) {
            throw new ShipXException("Cost Center with name [$costCenterName] not found.");
        }

        $this->mpk = $costCenterName;

        return $this;
    }

    public function payload(): array
    {
        self::applyCashOnDeliveryInsurance();
        self::applyInsuranceCostSaver();
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

    private function applyInsuranceCostSaver(): void
    {
        // if cod is filled, insurance cost saver cannot be applied
        if ($this->cash_on_delivery->isFilled()) {
            return;
        }

        // by default, all shipments are insured up to 500 PLN
        if ($this->insurance->get() < config('inpost.minimum_insurance_value', 500)) {
            $this->insurance->set(0);
        }
    }

    private function applyCashOnDeliveryInsurance(): void
    {
        if ($this->cash_on_delivery->isFilled() && $this->cash_on_delivery->get() > $this->insurance->get()) {
            $this->insurance->set($this->cash_on_delivery->get());
        }
    }

}