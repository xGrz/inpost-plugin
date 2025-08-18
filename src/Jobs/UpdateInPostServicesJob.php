<?php

namespace Xgrz\InPost\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostAdditionalService;
use Xgrz\InPost\Models\InPostService;

class UpdateInPostServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Collection $inpostServices;

    public function __construct()
    {
        $this->inpostServices = InPost::services()->getFromInPost();
    }

    private function updateLocalServices(): void
    {
        InPostService::query()->delete();

        foreach ($this->inpostServices as $inpostService) {
            $local = InPostService::withTrashed()->find($inpostService['id'])
                ?? new InPostService();

            $local->fill([
                'id' => $inpostService['id'],
                'name' => $inpostService['name'],
                'description' => $inpostService['description'],
            ]);

            when($local->active === NULL, fn() => $local->active = true);
            when($local->trashed(), fn() => $local->deleted_at = NULL);

            $local->save();
        }
    }

    private function updateLocalAdditionalServices(): void
    {
        InPostService::query()
            ->with(['additionalServices' => fn($query) => $query->withTrashed()])
            ->get()
            ->keyBy('id')
            ->each(fn(InPostService $service) => self::syncAdditionalServicesToInPostService($service));
    }

    private function syncAdditionalServicesToInPostService(InPostService $service): void
    {
        $inpostAdditionalServices = $this->inpostServices->firstWhere('id', $service->id)['additional_services'] ?? [];
        $service->additionalServices->each(function(InPostAdditionalService $additionalService) {
            if ($additionalService->deleted_at === NULL) {
                $additionalService->deleted_at = NULL;
            }
        });

        foreach($inpostAdditionalServices as $inpostAdditionalService) {
            $localAdditionalService = $service
                ->additionalServices
                ->keyBy('ident')
                ->get($inpostAdditionalService['id'])
                ?? new InPostAdditionalService();

            $localAdditionalService->fill([
                'ident' => $inpostAdditionalService['id'],
                'name' => $inpostAdditionalService['name'],
                'description' => $inpostAdditionalService['description'],
            ]);

            $localAdditionalService->deleted_at = NULL;
            when($localAdditionalService->active === NULL, fn() => $localAdditionalService->active = true);

            $service->additionalServices()->save($localAdditionalService);
        }
    }

    public function handle(): void
    {
        self::updateLocalServices();
        self::updateLocalAdditionalServices();
    }
}
