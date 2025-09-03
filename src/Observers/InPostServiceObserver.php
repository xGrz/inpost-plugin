<?php

namespace Xgrz\InPost\Observers;

use Xgrz\InPost\Models\InPostService;

class InPostServiceObserver
{
    public function creating(InPostService $inPostService): void
    {
        if (empty($inPostService->position)) {
            $inPostService->position = InPostService::max('position') + 1;
        }
    }

    public function updating(InPostService $inPostService): void
    {
        if ($inPostService->active === false) {
            $inPostService->position = null;
        }
        if ($inPostService->active === true && is_null($inPostService->position)) {
            $inPostService->position = InPostService::max('position') + 1;
        }
    }

    public function deleting(InPostService $inPostService): void
    {
        $inPostService->position = null;
    }

    public function restoring(InPostService $inPostService): void
    {
        $inPostService->position = InPostService::max('position') + 1;
    }
}
