<?php

namespace Xgrz\InPost\DTOs\CashOnDelivery;

use Illuminate\Support\Str;

/**
 * @property int    $amount
 * @property string $currency
 */
class CashOnDelivery
{
    protected int $amount = 0;
    protected ?string $currency = NULL;

    public function __set(string $name, $value): void
    {
        if ($name === 'amount') {
            $this->amount = Str::of($value * 100)->toInteger();
        }
        if ($name === 'currency') {
            $this->currency = $value;
        }
    }

    public function toArray(): array
    {
        if ($this->amount == 0) {
            return [
                'cod' => NULL,
                'cod_currency' => NULL,
            ];
        }

        return [
            'cod' => $this->amount / 100,
            'cod_currency' => $this->currency ?? 'PLN',
        ];
    }

    public function payload(): ?array
    {
        if ($this->amount == 0) {
            return NULL;
        }
        return [
            'amount' => $this->amount / 100,
            'currency' => $this->currency ?? 'PLN',
        ];
    }


}