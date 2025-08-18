<?php

namespace Xgrz\InPost\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Xgrz\InPost\Models\InPostShipmentNumber;

class ShipXWebhookController
{
    public function index()
    {
        return response('', 200);
    }

    public function consumeWebhook(Request $request)
    {
        if ($this->processEvent($request->toArray())) {
            return response('Confirmed', 200);
        }

        Log::alert(
            'Unknown event: ' . $request->get('event'),
            $request->toArray()
        );

        abort(404);
    }

    private function processEvent($event): bool
    {
        if ($event['event'] === 'shipment_confirmed') {
            $payload = $event['payload'];
            InPostShipmentNumber::find($payload['shipment_id'])
                ?->update(['tracking' => $payload['tracking_number'],]);

            return true;
        }
        return false;
    }
}
