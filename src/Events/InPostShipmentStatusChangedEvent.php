<?php

namespace Xgrz\InPost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Xgrz\InPost\Facades\InPost;
use Xgrz\InPost\Models\InPostShipmentNumber;

class InPostShipmentStatusChangedEvent
{
    use Dispatchable;

    public function __construct(public InPostShipmentNumber $shipment)
    {
    }

    public function getInPostIdent()
    {
        return $this->shipment->inpost_ident;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->shipment->tracking_number;
    }

    public function getStatus(): ?string
    {
        return $this->shipment->status;
    }

    public function getStatusDetails(): ?array
    {
        return InPost::getStatusDescription($this->shipment->status);
    }
}
