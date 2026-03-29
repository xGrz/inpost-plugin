<?php

namespace Xgrz\InPost\Actions;

use Carbon\Carbon;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Jobs\SynchronizePointsJob;
use Xgrz\InPost\Models\InPostPoint;

class SynchronizePointsAction
{
    private ?Carbon $startDate = NULL;
    private int $totalPages;

    public static function make(bool $shouldResetPoints = false): static
    {
        return new static($shouldResetPoints);
    }

    private function __construct(bool $shouldResetPoints = false)
    {
        if ($shouldResetPoints) {
            InPostPoint::truncate();
        }
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

    public static function getSearchParams(?Carbon $startDate = NULL, ?int $page = NULL): array
    {
        return array_filter(
            [
                'fields' => 'name',
                'updated_from' => $startDate?->format('Y-m-d'),
                'per_page' => config('inpost.synchronize_points_chunk_size', 250),
                'page' => $page === 1 ? NULL : $page,
            ],
            fn($value) => $value !== NULL
        );
    }

    public function getPagesCount(): ?int
    {
        $this->totalPages = InPost::points(self::getSearchParams($this->startDate))['total_pages'];
        return $this->totalPages;
    }

    public function dispatchJobs(): void
    {
        if ($this->totalPages === 0) return;

        for ($jobId = 1; $jobId <= $this->totalPages; $jobId++) {
            dispatch(new SynchronizePointsJob($this->startDate, $jobId));
        }
    }

}
