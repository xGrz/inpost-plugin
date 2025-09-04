<?php

namespace Xgrz\InPost\ApiRequests;


use Xgrz\InPost\ApiResponses\ShipmentCreated;
use Xgrz\InPost\ApiResponses\ShipmentCreateFail;
use Xgrz\InPost\Facades\InPostShipment;

class SendShipment extends BaseShipXCall
{
    protected string $endpoint = '/v1/organizations/:id/shipments';

    public function post(InPostShipment $shipment): ShipmentCreated|ShipmentCreateFail
    {
        $this->payload = $shipment->payload();
        $inPostResponse = $this->postCall();

        return $inPostResponse['status'] === 'created'
            ? new ShipmentCreated($inPostResponse)
            : new ShipmentCreateFail($inPostResponse);
    }
}
