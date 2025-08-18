<?php

namespace Xgrz\InPost\Facades;

use Illuminate\Support\Collection;
use Xgrz\InPost\ApiRequests\Services;
use Xgrz\InPost\Models\InPostAdditionalService;
use Xgrz\InPost\Models\InPostService;

class InPostServices
{

    public function getFromInPost(): Collection
    {
        return collect((new Services)->get() ?? []);
    }

    public function list(): Collection
    {
        return InPostService::all();
    }

    public function available(): Collection
    {
        return InPostService::where('active', true)->get();
    }

    public function additionalList(InPostService $service): Collection
    {
        return InPostAdditionalService::query()
            ->where('inpost_service_id', $service->id)
            ->get();
    }

    public function additionalAvailable(InPostService $service): Collection
    {
        return InPostAdditionalService::query()
            ->where('inpost_service_id', $service->id)
            ->where('active', true)
            ->get();
    }

    public function changeServiceAvailability(InPostService $service, ?bool $active = NULL): void
    {
        $service->active = $active ?? ! $service->active;
        $service->save();
    }

    public function changeAdditionalServiceAvailability(InPostAdditionalService $additionalService, ?bool $active = NULL): void
    {
        $additionalService->active = $active ?? ! $additionalService->active;
        $additionalService->save();
    }

}