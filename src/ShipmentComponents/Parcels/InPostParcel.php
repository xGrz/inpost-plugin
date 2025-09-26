<?php

namespace Xgrz\InPost\ShipmentComponents\Parcels;

use Xgrz\InPost\Interfaces\ParcelInterface;

class InPostParcel implements ParcelInterface
{


    public static function make(int $width, int $height, int $length, int|float $weight, int $quantity = 1, bool $is_non_standard = false): static
    {
        return new static($width, $height, $length, $weight, $quantity, $is_non_standard);
    }

    public function __construct(
        protected int       $width,
        protected int       $height,
        protected int       $length,
        protected int|float $weight,
        protected int       $quantity = 1,
        protected bool      $non_standard = false
    )
    {
    }

    public function __set(string $name, int|float|bool $value): void
    {
        if (property_exists($this, $name)) {
            $this->{$name} = $value;
        }
    }

    public function toArray(): array
    {
        return [
            'width' => $this->width,
            'height' => $this->height,
            'length' => $this->length,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
            'non_standard' => $this->non_standard,
            'template' => NULL
        ];
    }

    public function payload(): array
    {
        return [
            'dimensions' => [
                'width' => $this->width * 10,
                'height' => $this->height * 10,
                'length' => $this->length * 10,
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => $this->weight,
                'unit' => 'kg',
            ],
            'non_standard' => $this->non_standard,
        ];
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}