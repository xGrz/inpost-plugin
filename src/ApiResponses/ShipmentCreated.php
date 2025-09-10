<?php

namespace Xgrz\InPost\ApiResponses;

use Xgrz\InPost\Models\InPostShipmentNumber;

class ShipmentCreated extends BaseShipXResponse
{
    readonly private object $response;

    public function __construct(array $successResponse)
    {
        $this->response = json_decode(json_encode($successResponse));
        $this->storeShipmentMeta();
    }

    public function __get(string $name)
    {
        return $this->response->$name ?? NULL;
    }

    private function storeShipmentMeta(): void
    {
        InPostShipmentNumber::create([
            'inpost_ident' => $this->response->id,
            'status' => $this->response->status,
        ]);

    }

}
