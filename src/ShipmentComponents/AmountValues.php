<?php

namespace Xgrz\InPost\ShipmentComponents;

use Illuminate\Support\Str;
use Xgrz\InPost\Exceptions\ShipXException;

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

    /**
     * @throws ShipXException
     */
    public function set(int|float|string|null $amount, ?string $currency = 'PLN'): static
    {
        if (is_null($amount)) $amount = 0;
        if (is_string($amount)) {
            $baseAmount = $amount;
            $amount = str($amount)
                ->replaceMatches('/[^0-9.,]/', '')
                ->replaceFirst(',', '.')
                ->toString();
            if (! is_numeric($amount)) throw new ShipXException('Invalid amount value: [' . $baseAmount . '] format.');
            $amount = (float)$amount;
        }
        $this->amount = Str::of($amount * 100)->toInteger();
        $this->currency = $currency;
        return $this;
    }

    public function get(): ?float
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