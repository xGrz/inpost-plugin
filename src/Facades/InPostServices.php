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
        return InPostService::query()
            ->where('active', true)
            ->orderBy('position')
            ->get();
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


    public function additionalServicesSchema(InPostService|string $service): Collection
    {
        $schema = [];

        $service = is_string($service)
            ? InPostService::find($service)
            : $service;

        if (! $service || ! $service->active) {
            return collect();
        }

        $services = self::additionalAvailable($service)->keyBy('ident');


        if ($services->has('cod')) {
            $schema[] = [
                'id' => $services['cod']['ident'],
                'type' => 'money',
                'label' => $services['cod']['name'],
                'description' => $services['cod']['description'],
            ];
        }
        if ($services->has('insurance')) {
            $schema[] = [
                'id' => $services['insurance']['ident'],
                'type' => 'money',
                'label' => $services['insurance']['name'],
                'description' => $services['insurance']['description'],
            ];
        }
        if ($services->has('sms')) {
            $schema[] = [
                'id' => $services['sms']['ident'],
                'type' => 'checkbox',
                'label' => $services['sms']['name'],
                'description' => $services['sms']['description'],
            ];
        }
        if ($services->has('email')) {
            $schema[] = [
                'id' => $services['email']['ident'],
                'type' => 'checkbox',
                'label' => $services['email']['name'],
                'description' => $services['email']['description'],
            ];
        }
        if ($services->has('saturday')) {
            $schema[] = [
                'id' => $services['saturday']['ident'],
                'type' => 'checkbox',
                'label' => $services['saturday']['name'],
                'description' => $services['saturday']['description'],
            ];
        }
        if ($services->has('dor1720')) {
            $schema[] = [
                'id' => $services['dor1720']['ident'],
                'type' => 'checkbox',
                'label' => $services['dor1720']['name'],
                'description' => $services['dor1720']['description'],
            ];
        }

        $hourServices = $services->filter(fn($key) => str($key->ident)->startsWith('forhour_'));

        if ($hourServices->isNotEmpty()) {
            $schema[] = [
                'id' => 'forhour',
                'type' => 'selectable',
                'description' => '',
                'options' => $hourServices->map(fn($service) => ['id' => $service->ident, 'label' => $service->name])->toArray()
            ];
        }
        return collect($schema)->keyBy('id');
    }
}
