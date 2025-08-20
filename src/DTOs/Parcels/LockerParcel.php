<?php

namespace Xgrz\InPost\DTOs\Parcels;

use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Interfaces\ParcelInterface;

class LockerParcel implements ParcelInterface
{
    protected int $quantity = 1;
    protected ParcelLockerTemplate $parcel_template;

    public static function make(string|ParcelLockerTemplate $parcel_template, int $quantity = 1): static
    {
        return new static($parcel_template, $quantity);
    }

    private function __construct(string|ParcelLockerTemplate $parcel_template , int $quantity = 1)
    {
        $this->quantity = $quantity;

        if (! $parcel_template instanceof ParcelLockerTemplate) {
            $parcel_template = ParcelLockerTemplate::from($parcel_template);
        }
        $this->parcel_template = ! $parcel_template instanceof ParcelLockerTemplate
            ? ParcelLockerTemplate::from($parcel_template)
            : $parcel_template;
    }

    public function toArray(): array
    {
        return [
            'width' => $this->parcel_template->getWidth(),
            'height' => $this->parcel_template->getHeight(),
            'length' => $this->parcel_template->getLength(),
            'weight' => $this->parcel_template->getMaxWeight(),
            'quantity' => $this->quantity,
            'non_standard' => false,
            'template' => $this->parcel_template->value,
        ];
    }

    public function payload(): array
    {
        return [
            'template' => $this->parcel_template->value,
        ];
    }

    public function courierPayload(): array
    {
        return [
            'dimensions' => [
                'width' => $this->parcel_template->getWidth() * 10,
                'height' => $this->parcel_template->getHeight() * 10,
                'length' => $this->parcel_template->getLength() * 10,
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $this->parcel_template->getMaxWeight(),
                'unit' => 'kg',
            ],
            'non_standard' => false,
        ];
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

}