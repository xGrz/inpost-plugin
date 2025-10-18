<?php

namespace Xgrz\InPost\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Xgrz\InPost\Casts\PostCodeCast;
use Xgrz\InPost\Enums\PointStatus;

class InPostPoint extends Model
{
    protected $table = 'inpost_points';
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'longitude' => 'float',
            'latitude' => 'float',
            'status' => PointStatus::class,
            'functions' => 'array',
            'location_247' => 'boolean',
            'payment_available' => 'boolean',
            'payment_type' => 'array',
            'post_code' => PostCodeCast::class,
        ];
    }
    public function scopeName(Builder $query, string $name): void
    {
        $query->where('name', $name);
    }

    public function scopeOperating(Builder $query, PointStatus|true $operating = true): void
    {
        if ($operating === true) {
            $operating = PointStatus::OPERATING;
        }
        $query->where('status', $operating);
    }

    public function isParcelLocker(): bool
    {
        return ! empty($this->physical_type_mapped);
    }

    public function getLabelAttribute(): string
    {
        return $this->name . ' (' . ($this->location_description ?? $this->city . ', ' . $this->street) . ')';
    }
}
