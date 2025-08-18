<?php

use Xgrz\InPost\Http\Controllers\ShipXWebhookController;

Route::middleware(['inpost-ip-restriction', 'api'])
    ->withoutMiddleware('web')
    ->name('inpost.')
    ->prefix('inpost')
    ->group(function() {
        Route::get('/webhook', [ShipXWebhookController::class, 'index'])->name('index');
        Route::post('/webhook', [ShipXWebhookController::class, 'consumeWebhook'])->name('webhook');
    });
