<?php

namespace Xgrz\InPost\Facades;

use Illuminate\Support\Collection;
use Xgrz\InPost\ApiRequests\CostCenter;
use Xgrz\InPost\ApiRequests\Organization;
use Xgrz\InPost\ApiRequests\Services;
use Xgrz\InPost\ApiRequests\Statuses;

class InPost
{
    public static function organization()
    {
        return (new Organization)->get();
    }

    public static function statuses()
    {
        return (new Statuses)->get();
    }

    public static function getStatusDescription(string $statusName): ?array
    {
        return cache()
            ->remember(
                config('inpost.cache.statuses.key', 'inpost-statuses'),
                config('inpost.cache.statuses.ttl', 86400),
                fn(): Collection => collect(self::statuses())->keyBy('name')
            )
            ?->get($statusName);
    }

    public static function services()
    {
        return (new Services)->get();
    }

    public static function getServiceDescription(string $serviceName): ?array
    {
        return cache()
            ->remember(
                config('inpost.cache.services.key', 'inpost-services'),
                config('inpost.cache.services.ttl', 86400),
                fn(): Collection => collect(self::services())->keyBy('id')
            )
            ?->get($serviceName);
    }


    public static function costCenters(): CostCenter
    {
        return (new CostCenter);
    }
}
