<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\ApiRequests\SendShipment;
use Xgrz\InPost\ApiResponses\ShipmentCreated;
use Xgrz\InPost\ApiResponses\ShipmentCreateFail;
use Xgrz\InPost\Exceptions\ShipXException;
use Xgrz\InPost\ShipmentComponents\Address\Receiver;
use Xgrz\InPost\ShipmentComponents\Address\Sender;
use Xgrz\InPost\ShipmentComponents\CashOnDelivery\CashOnDelivery;
use Xgrz\InPost\ShipmentComponents\Insurance\Insurance;
use Xgrz\InPost\ShipmentComponents\Parcels\Parcels;
use Xgrz\InPost\ShipmentComponents\Services\ShipmentService;

class InPostShipment
{
    public readonly Sender $sender; // if not provided, will be set default InPost data
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

    public static function make(?string $shipmentService = NULL, ?Receiver $receiver = NULL, ?Sender $sender = NULL): static
    {
        return new static($shipmentService, $receiver, $sender);
    }

    public function __construct(?string $shipmentService = NULL, ?Receiver $receiver = NULL, ?Sender $sender = NULL)
    {
        $this->sender = $sender ?? new Sender();
        $this->receiver = $receiver ?? new Receiver();
        $this->parcels = Parcels::make();
        $this->cash_on_delivery = new CashOnDelivery();
        $this->insurance = new Insurance();
        $this->service = ShipmentService::make();
        if (! empty($shipmentService)) {
            $this->service->setService($shipmentService);
        }
    }

    public function comment(?string $comment): static
    {
        $this->comments = $comment;
        return $this;
    }

    public function reference(?string $reference): static
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
    public function targetPoint(string $targetPoint, ?string $email = NULL, ?string $phone = NULL, ?string $serviceName = NULL): static
    {
        $this->service->targetPoint($targetPoint);
        if (! empty($email)) {
            $this->receiver->email = $email;
        }
        if (! empty($phone)) {
            $this->receiver->phone = $phone;
        }
        if (! empty($serviceName)) {
            $this->service->setService($serviceName);
        }
        return $this;
    }

    /**
     * @throws ShipXException
     */
    public function costCenter(?string $costCenterName): static
    {
        if (empty($costCenterName)) {
            return $this;
        }
        $exists = InPost::costCenters()
            ->get()
            ->filter(fn($costCenter) => $costCenter['name'] === $costCenterName)
            ->first();

        if (! $exists) {
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
                'sender' => $this->sender->payload(),
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

    public function toArray(): array
    {
        self::applyCashOnDeliveryInsurance();
        self::applyInsuranceCostSaver();

        $data = [];
        $sender = $this->sender->toArray();

        if ($sender) {
            $data['sender'] = $sender;
        }

        $data += [
            'receiver' => $this->receiver->toArray(),
            'parcels' => $this->parcels->toArray(),
            'reference' => $this->reference,
            'is_return' => $this->is_return,
            'comment' => $this->comments,
            'only_choice_of_offer' => $this->only_choice_of_offer,
            'cost_center' => $this->mpk,
        ];

        $data += $this->service->toArray();
        $data += $this->cash_on_delivery->toArray();
        $data += $this->insurance->toArray();

        return $data;
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

    public function send(): ShipmentCreateFail|ShipmentCreated
    {
        return (new SendShipment())->post($this);
    }

}