<?php

namespace Xgrz\InPost\DTOs\Insurance;

use Illuminate\Support\Str;

/**
 * @property int    $amount
 * @property string $currency
 */
class Insurance
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
                'insurance' => NULL,
                'insurance_currency' => NULL,
            ];
        }

        return [
            'insurance' => $this->amount / 100,
            'insurance_currency' => $this->currency ?? 'PLN',
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