<?php

namespace Xgrz\InPost\Services;

use Illuminate\Support\Collection;
use Xgrz\InPost\Casts\PostCodeCast;
use Xgrz\InPost\DTOs\Point;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostPoint;

class PointSearchService
{
    static public array $localSearchableColumns = ['name', 'city', 'street', 'location_description'];
    public ?Collection $searchWords = NULL;
    public ?Collection $searchPostCodes = NULL;
    public ?Collection $results = NULL;

    public function __construct(?string $query = NULL)
    {
        self::discoverSearchWords($query);
        self::discoverPostCodes($query);

        $points = self::searchInLocalDatabase();
        $postCode = self::searchNearbyPostCodes();

        $postCode->each(function($point, $name) use ($points) {
            $points->put($name, $point);
        });

        $this->results = $points->sortBy(fn(Point $point) => $point->distance ?? PHP_INT_MAX, SORT_NUMERIC);
    }

    public static function make(?string $query = NULL): Collection
    {
        return (new static($query))->results;
    }

    private function discoverSearchWords(?string $query = NULL): void
    {
        if (empty($query)) return;

        $this->searchWords = collect(explode(' ', $query))
            ->filter(fn($word) => ! PostCodeCast::isPostCode($word))
            ->unique()
            ->flatten();
    }

    private function discoverPostCodes(?string $query = NULL): void
    {
        if (empty($query)) return;
        $this->searchPostCodes = collect(explode(' ', $query))
            ->filter(fn($word) => PostCodeCast::isPostCode($word))
            ->map(fn($word) => PostCodeCast::format($word))
            ->unique()
            ->flatten();
    }

    private function searchInLocalDatabase(): Collection
    {
        if ($this->searchWords->isEmpty()) return new Collection();

        $searchQuery = InPostPoint::query()
            ->operating();

        $this->searchWords
            ->each(function($word) use ($searchQuery) {
                $searchQuery->where(function($query) use ($word) {
                    foreach (static::$localSearchableColumns as $column) {
                        $query->orWhere($column, 'like', "%{$word}%");
                    }
                });
            });

        return $searchQuery
            ->get()
            ->map(fn(InPostPoint $point) => Point::fromInPostPointModel($point))
            ->keyBy('name');
    }

    private function searchNearbyPostCodes(): Collection
    {
        if ($this->searchPostCodes->isEmpty()) return new Collection();

        $nearbyInPostPoints = self::searchAPIByPostCodeWithDistance($this->searchPostCodes->first());
        return InPostPoint::query()
            ->whereIn('name', $nearbyInPostPoints->keys())
            ->get()
            ->map(fn(InPostPoint $point) => Point::fromInPostPointModel($point))
            ->map(function(Point $point) use ($nearbyInPostPoints) {
                $point->distance($nearbyInPostPoints->get($point->name));
                return $point;
            })
            ->keyBy('name');
    }

    private static function searchAPIByPostCodeWithDistance(string $postCode): Collection
    {
        $apiResponse = InPost::points([
            'relative_post_code' => $postCode,
            'limit' => 50,
            'per_page' => 50,
            'fields' => 'name,distance',
            'sort_by' => 'distance',
            'max_distance' => 10000,
        ]);
        return collect($apiResponse['items'] ?? [])
            ->pluck('distance', 'name');
    }
}
