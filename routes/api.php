<?php

use Xgrz\InPost\Config\InPostConfig;
use Xgrz\InPost\Http\Controllers\ShipXWebhookController;

Route::middleware(['inpost-ip-restriction', 'api'])
    ->withoutMiddleware('web')
    ->name('inpostWebhook.')
    ->prefix(InPostConfig::webhookUrl())
    ->group(function() {
        Route::get('', [ShipXWebhookController::class, 'index'])->name('index');
        Route::post('', [ShipXWebhookController::class, 'consumeWebhook'])->name('consume');
    });


