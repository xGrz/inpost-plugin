<?php

namespace Xgrz\InPost\Facades;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Xgrz\InPost\ApiRequests\CostCenter;
use Xgrz\InPost\ApiRequests\Label;
use Xgrz\InPost\ApiRequests\Organization;
use Xgrz\InPost\ApiRequests\Points;
use Xgrz\InPost\ApiRequests\SendingMethods;
use Xgrz\InPost\ApiRequests\Services;
use Xgrz\InPost\ApiRequests\Statuses;
use Xgrz\InPost\ApiRequests\Tracking;
use Xgrz\InPost\Enums\InPostParcelLocker;
use Xgrz\InPost\Exceptions\ShipXShipmentNotFoundException;
use Xgrz\InPost\Services\PointSearchService;

class InPost
{
    public static function organization()
    {
        return (new Organization)->get();
    }

    public static function statuses()
    {
        return cache()
            ->remember(
                config('inpost.cache.statuses.key', 'inpost-statuses'),
                config('inpost.cache.statuses.ttl', 60),
                fn() => collect((new Statuses)->get())
            );
    }

    public static function getStatusDescription(string $statusName): ?array
    {
        return self::statuses()->keyBy('name')->get($statusName);
    }

    public static function services(): Collection
    {
        return cache()
            ->remember(
                'inpost:services',
                now()->endOfDay(),
                fn() => collect((new Services)->get() ?? [])->keyBy('id')
            );
    }

    public static function extraService(string $serviceName): Collection
    {
        return collect(self::services()->get($serviceName)['additional_services'] ?? [])->keyBy('id');
    }

    public static function hasExtraService(string $serviceName, string $extraServiceName): bool
    {
        return self::extraService($serviceName)->has($extraServiceName);
    }


    public static function costCenters(): CostCenter
    {
        return (new CostCenter);
    }

    /**
     * @throws ConnectionException
     */
    public static function label(string $inPostShipmentId): string
    {
        return (new Label())->get($inPostShipmentId);
    }

    public static function parcelLockerTemplates(): Collection
    {
        return InPostParcelLocker::optionsForLocker();
    }

    public static function parcelAddressTemplates(): Collection
    {
        return InPostParcelLocker::optionsForAddress();
    }

    /**
     * @throws ShipXShipmentNotFoundException
     * @throws ConnectionException
     */
    public static function trackingInfo(string $trackingNumber)
    {
        return (new Tracking)->get($trackingNumber);
    }

    /**
     * @throws ShipXShipmentNotFoundException
     * @throws ConnectionException
     */
    public static function trackingEvents(string $trackingNumber)
    {
        return (new Tracking)->get($trackingNumber)['tracking_details'] ?? [];
    }

    public static function sendingMethods()
    {
        return (new SendingMethods())->get();
    }

    public static function points(array $search = [])
    {
        return (new Points())->get($search);
    }

    public static function pointSearch(?string $query): Collection
    {
        return PointSearchService::make($query);
    }


}
