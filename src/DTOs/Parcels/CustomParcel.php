<?php

namespace Xgrz\InPost\DTOs\Parcels;

use Xgrz\InPost\Interfaces\ParcelInterface;

class CustomParcel implements ParcelInterface
{
    protected int $width;
    protected int $height;
    protected int $length;
    protected float $weight;
    protected int $quantity = 1;
    protected bool $non_standard;

    public static function make(int $width, int $height, int $length, int|float $weight, int $quantity = 1, bool $is_non_standard = false): static
    {
        return new static($width, $height, $length, $weight, $quantity, $is_non_standard);
    }

    public function __construct(int $width, int $height, int $length, int|float $weight, int $quantity = 1, bool $is_non_standard = false)
    {
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->weight = $weight;
        $this->quantity = $quantity;
        $this->non_standard = $is_non_standard;
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
        ];
    }

    public function payload(): array
    {
        return [
            'dimensions' => [
                'width' => round($this->width * 10),
                'height' => round($this->height * 10),
                'length' => round($this->length * 10),
                'unit' => 'mm',
            ],
            'weight' => [
                'amount' => round($this->weight, 1),
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