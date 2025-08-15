<?php

namespace Xgrz\InPost\Interfaces;

interface ParcelInterface
{
    public function payload(): mixed;

    public function getQuantity(): int;

    public function toArray(): array;
}