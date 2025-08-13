<?php

namespace Xgrz\InPost\ApiRequests;

use Illuminate\Http\Client\ConnectionException;
use Xgrz\InPost\Exceptions\ShipXShipmentNotFoundException;

class Tracking extends BaseShipXCall
{
    protected string $endpoint = '/v1/tracking/:tracking_number';

    /**
     * @throws ShipXShipmentNotFoundException
     * @throws ConnectionException
     */
    public function get(string $tracking_number)
    {
        $this->setProp('tracking_number', $tracking_number);
        $response = $this->getCall();

        if (isset($response['error'])) {
            ShipXShipmentNotFoundException::fromResponse($response);
        }

        return $response;
    }
}
