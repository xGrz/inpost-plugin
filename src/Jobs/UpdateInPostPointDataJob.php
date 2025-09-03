<?php

namespace Xgrz\InPost\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Xgrz\InPost\Enums\PointStatus;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostPoint;

class UpdateInPostPointDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

    public function __construct(public array $pointNames)
    {
    }

    private function fetchApiPoints(): Collection
    {
        $items = InPost::points([
            'fields' => implode(',', $this->retrieveFields),
            'name' => implode(',', $this->pointNames),
            'per_page' => 500,
        ])['items'];
        return collect($items);
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

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $apiPoints = $this->fetchApiPoints();
        $localPoints = InPostPoint::whereIn('name', $this->pointNames)->get();

        $apiPoints->each(function($point) use ($localPoints) {
            try {
                $localPoint = $localPoints->firstWhere('name', $point['name']) ?? new InPostPoint();
                $localPoint->fill($this->buildPointPayload($point));
                if ($localPoint->isDirty()) {
                    $localPoint->save();
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage(), $this->buildPointPayload($point));
            }
        });
    }


//    public function handle(): void
//    {
//        $apiPoints = $this->fetchApiPoints();
//        $localPoints = InPostPoint::whereIn('name', $this->pointNames)->get();
//
//        DB::transaction(function() use ($apiPoints, $localPoints) {
//            $apiPoints->each(function($point) use ($localPoints) {
//                $localPoint = $localPoints->firstWhere('name', $point['name']) ?? new InPostPoint();
//                $localPoint->fill($this->buildPointPayload($point));
//                if ($localPoint->isDirty()) {
//                    $localPoint->save();
//                }
//            });
//        });
//    }

}
