<?php

namespace Xgrz\InPost\ApiRequests;

class SendingMethods extends BaseShipXCall
{
    protected string $endpoint = '/v1/sending_methods';

    public function get()
    {
        return $this->getCall();
    }
}
