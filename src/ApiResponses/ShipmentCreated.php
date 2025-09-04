<?php

namespace Xgrz\InPost\ApiResponses;

class ShipmentCreated extends BaseShipXResponse
{
    readonly private object $response;

    public function __construct(array $successResponse)
    {
        $this->response = json_decode(json_encode($successResponse));
    }

    public function __get(string $name)
    {
        return $this->response->$name ?? NULL;
    }

}
