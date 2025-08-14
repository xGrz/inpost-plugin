<?php

namespace Xgrz\InPost\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostPoint;

class SynchronizeInPostPointsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?Carbon $startDate = NULL;
    private int $totalPages;

    public function __construct()
    {
        self::setupStartDate();
        self::getPagesCount();
    }

    private function setupStartDate(): void
    {
        $latestUpdate = InPostPoint::latest()->first()?->updated_at;
        if (! $latestUpdate) return;

        if ($latestUpdate->clone()->startOfDay()->addDays(3)->isFuture()) {
            $this->startDate = $latestUpdate->startOfDay();
        }
    }

    private function getSearchParams(?int $page = NULL): array
    {
        return array_filter(
            [
                'fields' => 'name',
                'updated_from' => $this->startDate?->format('Y-m-d'),
                'per_page' => config('inpost.synchronize_points_chunk_size', 250),
                'page' => $page === 1 ? NULL : $page,
            ],
            fn($value) => $value !== NULL
        );
    }

    private function getPagesCount(): void
    {
        $this->totalPages = InPost::points(self::getSearchParams())['total_pages'];
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        if ($this->totalPages === 0) return;
        for ($page = 1; $page <= $this->totalPages; $page++) {
            $apiRequest = InPost::points(self::getSearchParams($page))['items'];
            $points = collect($apiRequest)->keyBy('name')->keys()->toArray();
            dispatch(new UpdateInPostPointDataJob($points));
        }
    }
}
