<?php

namespace Xgrz\InPost\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Xgrz\InPost\Enums\PointStatus;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostPoint;

class SynchronizePointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 2;
    public int $chunkSize = 100;
    private array $retrieveFields = [
        'image_url',
        'name',
        'status',
        'location',
        'location_type',
        'location_description',
        'location_247',
        'address_details',
        'type',
        'functions',
        'payment_available',
        'payment_type',
        'payment_point_descr',
        'partner_id',
        'address_details',
        'physical_type_mapped',
        'physical_type_description',
    ];

    public function __construct(public ?Carbon $startDate, public int $apiPage, ?int $chunkSize = null)
    {
        $this->chunkSize = $chunkSize ?? config('inpost.synchronize_points_chunk_size', 500);
    }

    private function fetchApi()
    {
        return InPost::points([
            'fields' => implode(',', $this->retrieveFields),
            'updated_from' => $this->startDate?->format('Y-m-d'),
            'per_page' => $this->chunkSize,
            'page' => $this->apiPage,
        ])['items'];
    }

    private function buildPointPayload(array $point): array
    {
        return [
            'name' => $point['name'],
            'image_url' => $point['image_url'],
            'status' => PointStatus::from($point['status']),
            'latitude' => round($point['location']['latitude'], 8),
            'longitude' => round($point['location']['longitude'], 8),
            'location_type' => $point['location_type'],
            'location_description' => $point['location_description'],
            'street' => join(' ', [$point['address_details']['street'], $point['address_details']['building_number']]),
            'city' => $point['address_details']['city'],
            'post_code' => $point['address_details']['post_code'],
            'payment_available' => $point['payment_available'],
            'payment_point_description' => $point['payment_point_descr'],
            'payment_type' => $point['payment_type'],
            'functions' => $point['functions'],
            'location_247' => $point['location_247'],
            'partner_id' => (int)$point['partner_id'],
            'physical_type_mapped' => $point['physical_type_mapped'],
            'physical_type_description' => $point['physical_type_description'],
        ];
    }

    public function handle(): void
    {
        collect($this->fetchApi())
            ->each(fn($point) => InPostPoint::updateOrCreate(['name' => $point['name']], self::buildPointPayload($point)));
    }
}
