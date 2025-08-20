<?php

namespace Xgrz\InPost\ShipmentComponents\CashOnDelivery;

use Xgrz\InPost\ShipmentComponents\AmountValues;

class CashOnDelivery extends AmountValues
{

    public function toArray(): array
    {
        return $this->amount > 0
            ? ['cod' => $this->get(), 'cod_currency' => $this->getCurrency()]
            : ['cod' => NULL, 'cod_currency' => NULL];
    }


}