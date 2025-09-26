<?php

namespace Xgrz\InPost\ShipmentComponents\Parcels;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Xgrz\InPost\Enums\InPostParcelLocker;
use Xgrz\InPost\Interfaces\PayloadInterface;

class Parcels implements PayloadInterface
{
    private Collection $items;

    public function __construct()
    {
        $this->items = collect();
    }

    public static function make(): static
    {
        return new static();
    }

    public function add(InPostParcel|InPostParcelLocker $parcel): static
    {
        $this->items->push($parcel);
        return $this;
    }

    private function isParcelLockerPackage(): bool
    {
        return $this->items->count() === 1
            && $this->items->first() instanceof InPostParcelLocker
            && $this->items->first()->getQuantity() === 1;
    }

    public function payload(): array
    {
        if (self::isParcelLockerPackage()) {
            return $this->items->first()->payload();
        }

        $payload = collect();
        $this->items
            ->each(function(InPostParcel|InPostParcelLocker $parcel) use ($payload) {
                for ($i = 1; $i <= $parcel->getQuantity(); $i++) {
                    $payload->push($parcel->payload());
                }
            });

        return $payload
            ->map(fn(array $parcel) => ['id' => Str::random(4)] + $parcel)
            ->toArray();
    }

    public function toArray(): array
    {
        return $this->items->map(fn(InPostParcel|InPostParcelLocker $parcel) => $parcel->toArray())->toArray();
    }
}

