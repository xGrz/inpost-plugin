<?php

namespace Xgrz\InPost\ApiRequests;


class Services extends BaseShipXCall
{
    protected string $endpoint = '/v1/services';

    public function get()
    {
        return $this->getCall();
    }
}
