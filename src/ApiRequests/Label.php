<?php

namespace Xgrz\InPost\ApiRequests;

use Illuminate\Http\Client\ConnectionException;

class Label extends BaseShipXCall
{
    protected string $endpoint = '/v1/shipments/:shipment_id/label?format=:format&type=:type';

    /**
     * @throws ConnectionException
     */
    public function get(string $inPostShipmentId): string
    {
        $this->setProp('shipment_id', $inPostShipmentId);
        $this->setProp('format', 'pdf');
        $this->setProp('type', 'A6');
        return $this->getFile();
    }
}
