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
use Xgrz\InPost\Enums\ParcelLockerTemplate;
use Xgrz\InPost\Exceptions\ShipXShipmentNotFoundException;
use Xgrz\InPost\Models\ParcelTemplate;
use Xgrz\InPost\Services\PointSearchService;

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

    public static function services(): Collection
    {
        return collect((new Services)->get() ?? []);
    }

    public static function getServiceDescription(string $serviceName): ?array
    {
        return self::services()
            ->keyBy('id')
            ->get($serviceName);
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
        return ParcelLockerTemplate::optionsForLocker();
    }

    public static function parcelAddressTemplates(): Collection
    {
        return ParcelLockerTemplate::optionsForAddress();
    }

    public static function parcelCourierTemplates(): Collection
    {
        return collect(ParcelTemplate::all()->toArray());
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
