<?php

namespace Xgrz\InPost\ApiRequests;

class Statuses extends BaseShipXCall
{
    protected string $endpoint = '/v1/statuses';

    public function get()
    {
        $response = $this->getCall();

        return isset($response['error'])
            ? $response
            : $response['items'];
    }
}
