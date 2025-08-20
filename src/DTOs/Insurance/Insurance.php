<?php

namespace Xgrz\InPost\DTOs\Insurance;

use Xgrz\InPost\DTOs\AmountValues;

class Insurance extends AmountValues
{
    public function toArray(): array
    {
        return $this->amount > 0
            ? ['insurance' => $this->amount / 100, 'insurance_currency' => $this->currency]
            : ['insurance' => NULL, 'insurance_currency' => NULL];
    }

}