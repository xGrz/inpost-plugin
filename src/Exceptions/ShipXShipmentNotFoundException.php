<?php

namespace Xgrz\InPost\Exceptions;

class ShipXShipmentNotFoundException extends \Exception
{

    public string $apiError = '';
    public ?string $apiMessage = NULL;
    public mixed $apiDetails = NULL;
    public int $apiCode = 0;

    /**
     * @throws ShipXShipmentNotFoundException
     */
    public static function fromResponse(array $response)
    {
        $exception = new static($response['error']);
        $exception->apiError = $response['error'];
        $exception->apiMessage = $response['description'] ?? NULL;
        $exception->apiDetails = $response['details'] ?? NULL;
        $exception->apiCode = $response['status'] ?? 499;
        throw $exception;
    }

}
