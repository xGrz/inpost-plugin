<?php

namespace Xgrz\InPost\Tests\Webhook;

use Xgrz\InPost\Tests\InPostTestCase;

class WebhookAccessRestrictionTest extends InPostTestCase
{
    public function test_allows_access_from_ip_in_subnet()
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '91.216.25.100',
        ])->get('/inpost-webhook');

        $response->assertStatus(200);
        $response->assertSee('');
    }

    public function test_denies_access_from_ip_outside_subnet()
    {
        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '8.8.8.8',
        ])->get('/inpost-webhook');

        $response->assertStatus(404);
    }

    public function test_cloudeflare_ip_is_allowed()
    {
        $response = $this->withServerVariables([
            'HTTP_CF_CONNECTING_IP: 91.216.25.100',
            'REMOTE_ADDR' => '8.8.8.8',
        ])->get('/inpost-webhook');

        $response->assertStatus(404);
    }


    public function test_cloudeflare_ip_is_disallowed()
    {
        $response = $this->withServerVariables([
            'HTTP_CF_CONNECTING_IP: 100.100.25.100',
            'REMOTE_ADDR' => '8.8.8.8',
        ])->get('/inpost-webhook');

        $response->assertStatus(404);
    }

}