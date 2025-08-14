<?php

namespace Xgrz\InPost\DTOs;

use Xgrz\InPost\Enums\PointStatus;
use Xgrz\InPost\Models\InPostPoint;

class Point
{
    readonly public string $name;
    readonly public PointStatus $status;
    readonly public float $latitude;
    readonly public float $longitude;
    readonly public ?string $location_type;
    readonly public ?string $location_description;
    readonly public string $street;
    readonly public string $city;
    readonly public string $post_code;
    readonly public bool $payment_available;
    readonly public ?string $payment_point_description;
    readonly public array $payment_type;
    readonly public array $functions;
    readonly public bool $location_247;
    readonly public int $partner_id;
    readonly public ?string $physical_type_mapped;
    readonly public ?string $physical_type_description;
    public ?int $distance = NULL;

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if ($key === 'status') {
                    $value = PointStatus::from($value);
                }
                $this->{$key} = $value;
            }
        }
    }

    public function distance(?int $distance = NULL): static
    {
        $this->distance = $distance;
        return $this;
    }

    public static function fromInPostPointModel(InPostPoint $inPostPoint, ?int $distance = NULL): static
    {
        return new static($inPostPoint->toArray());
    }
}