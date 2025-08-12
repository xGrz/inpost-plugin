<?php

namespace Xgrz\InPost\Facades;

use Xgrz\InPost\ApiRequests\Organization;

class InPost
{

    public static function organization()
    {
        return (new Organization)->get();
    }
}
