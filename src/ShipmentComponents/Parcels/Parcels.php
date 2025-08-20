<?php

namespace Xgrz\InPost\ShipmentComponents\Parcels;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Interfaces\ParcelInterface;
use Xgrz\InPost\Interfaces\PayloadInterface;

class Parcels implements PayloadInterface
{
    private Collection $parcels;

    public function __construct()
    {
        $this->parcels = collect();
    }

    public static function make(): static
    {
        return new static();
    }

    public function add(ParcelInterface|ParcelLockerTemplate $parcel): static
    {
        if ($parcel instanceof ParcelLockerTemplate) {
            $parcel = LockerParcel::make($parcel);
        }

        $this->parcels->push($parcel);
        return $this;
    }

    private function isParcelLockerPackage(): bool
    {
        return $this->parcels->count() === 1
            && $this->parcels->first() instanceof LockerParcel
            && $this->parcels->first()->getQuantity() === 1;
    }

    public function payload(): array
    {
        if (self::isParcelLockerPackage()) {
            return $this->parcels->first()->payload();
        }

        $payload = collect();
        $this->parcels
            ->each(function(ParcelInterface $parcel) use ($payload) {
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
        return $this->parcels->map(fn(ParcelInterface $parcel) => $parcel->toArray())->toArray();
    }
}

