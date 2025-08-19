<?php

namespace Xgrz\InPost\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Xgrz\InPost\Events\InPostShipmentStatusChangedEvent;
use Xgrz\InPost\Http\Requests\WebhookRequest;
use Xgrz\InPost\Models\InPostShipmentNumber;

class ShipXWebhookController
{
    public function index()
    {
        return response('', 200);
    }

    public function consumeWebhook(WebhookRequest $request)
    {
        return match ($request->webhookEvent()) {
            'shipment_confirmed' => self::shipmentConfirmed($request->webhookPayload()),
            'shipment_status_changed' => self::statusChanged($request->webhookPayload()),
            default => self::unknownEvent($request),
        };
    }

    private static function unknownEvent(WebhookRequest $request)
    {
        Log::emergency('Unknown inpost-webhook event: ' . $request->get('event') ?? 'null', $request->webhookPayload());
        return response('Unknown event', 400);
    }

    private static function shipmentConfirmed(array $webhookPayload)
    {
        $shipment = InPostShipmentNumber::find($webhookPayload['shipment_id']);
        if (! $shipment) {
            return response('Not found', 404);
        }

        $shipment->update([
            'tracking_number' => $webhookPayload['tracking_number'],
            'status' => 'confirmed',
        ]);

        return response('', 200);
    }

    private static function statusChanged(array $webhookPayload)
    {
        $shipment = InPostShipmentNumber::find($webhookPayload['shipment_id']);
        if (! $shipment) {
            return response('Not found', 404);
        }

        $shipment->fill([
            'tracking_number' => $webhookPayload['tracking_number'],
            'status' => $webhookPayload['status'],
        ]);

        if (isset($webhookPayload['return_tracking_number'])) {
            $shipment->return_tracking_number = $webhookPayload['return_tracking_number'];
        }
        $shipment->save();

        InPostShipmentStatusChangedEvent::dispatch($shipment->tracking_number, $shipment->status);

        return response('', 200);
    }

}
