<?php

namespace Xgrz\InPost\Facades;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Xgrz\InPost\ApiRequests\CostCenter;
use Xgrz\InPost\ApiRequests\Label;
use Xgrz\InPost\ApiRequests\Organization;
use Xgrz\InPost\ApiRequests\Points;
use Xgrz\InPost\ApiRequests\SendingMethods;
use Xgrz\InPost\ApiRequests\Statuses;
use Xgrz\InPost\ApiRequests\Tracking;
use Xgrz\InPost\DTOs\ParcelTemplateDTO;
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

    public static function services(): InPostServices
    {
        return new InPostServices();
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
        return ParcelLockerTemplate::optionsForLocker()
            ->map(fn($item) => ParcelTemplateDTO::make($item));
    }

    public static function parcelAddressTemplates(): Collection
    {
        return ParcelLockerTemplate::optionsForAddress()
            ->map(fn($item) => ParcelTemplateDTO::make($item));
    }

    public static function parcelCourierTemplates(): Collection
    {
        return ParcelTemplate::all()
            ->map(fn($item) => ParcelTemplateDTO::make($item));
    }

    public static function parcelTemplates(): Collection
    {
        $locker = self::parcelLockerTemplates()->keyBy('name');
        $address = self::parcelAddressTemplates()->keyBy('name');
        $courier = self::parcelCourierTemplates()->keyBy('name');

        return $locker->merge($address)->merge($courier);
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
