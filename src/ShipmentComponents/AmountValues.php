<?php

namespace Xgrz\InPost\ShipmentComponents;

use Illuminate\Support\Str;

/**
 * @property int    $amount
 * @property string $currency
 */
abstract class AmountValues
{
    protected int $amount = 0;
    protected ?string $currency = NULL;

    public function isFilled(): bool
    {
        return $this->amount > 0;
    }

    public function set(int|float $amount, ?string $currency = 'PLN'): static
    {
        $this->amount = Str::of($amount * 100)->toInteger();
        $this->currency = $currency;
        return $this;
    }

    public function get(): float
    {
        return $this->amount / 100;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    abstract public function toArray(): array;

    public function payload(): ?array
    {
        return $this->amount > 0
            ? ['amount' => $this->get(), 'currency' => $this->getCurrency()]
            : NULL;
    }

}