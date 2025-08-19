<?php

namespace Xgrz\InPost\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Xgrz\InPost\Facades\InPost;

class InPostShipmentStatusChangedEvent
{
    use Dispatchable;

    public function __construct(protected string $trackingNumber, protected string $status)
    {
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusDetails(): ?array
    {
        return InPost::getStatusDescription($this->status);
    }
}
