<?php

namespace Xgrz\InPost\ApiRequests;

class Organization extends BaseShipXCall
{
    protected string $endpoint = '/v1/organizations/:id';

    public function get()
    {
        return $this->getCall();
    }
}
