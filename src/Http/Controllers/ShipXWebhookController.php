<?php

namespace Xgrz\InPost\Http\Controllers;

use Illuminate\Http\Request;
use Xgrz\InPost\Models\InPostShipmentNumber;

class ShipXWebhookController
{
    public function __invoke(Request $request)
    {
        if ($this->processEvent($request->toArray())) {
            return response('Confirmed', 200);
        }

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
