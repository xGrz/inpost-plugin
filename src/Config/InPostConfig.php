<?php

namespace Xgrz\InPost\Config;

use Xgrz\InPost\Exceptions\ShipXConfigurationException;

class InPostConfig
{
    public static function webhookUrl(): string
    {
        return str(config('inpost.webhook_url', '/inpost-webhook'))
            ->replaceStart('/', '')
            ->replaceEnd('/', '')
            ->toString();
    }

    public static function webhookFullUrl(): string
    {
        return route('inpostWebhook');
    }

    /**
     * @throws ShipXConfigurationException
     */
    public static function token(): string
    {
        $token = config('inpost.token');
        if (empty($token)) throw new ShipXConfigurationException('Missing InPost API token');
        return config('inpost.token');
    }

    /**
     * @throws ShipXConfigurationException
     */
    public static function baseUrl(): string
    {
        $url = config('inpost.url');
        if (empty($url)) throw new ShipXConfigurationException('Missing InPost API url');
        return config('inpost.url');
    }

    /**
     * @throws ShipXConfigurationException
     */
    public static function organizationId(): string
    {
        $organizationId = config('inpost.organization');
        if (empty($organizationId)) throw new ShipXConfigurationException('Missing organization id');
        return config('inpost.organization');
    }
}