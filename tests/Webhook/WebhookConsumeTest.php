<?php

namespace Xgrz\InPost\Tests\Webhook;

use Illuminate\Support\Facades\Event;
use Xgrz\InPost\Events\InPostShipmentStatusChangedEvent;
use Xgrz\InPost\Models\InPostShipmentNumber;
use Xgrz\InPost\Tests\InPostTestCase;

class WebhookConsumeTest extends InPostTestCase
{

    public function test_return_bad_request_if_event_is_unknown()
    {
        $payload = $this->fakeRequestPayload('ShipmentConfirmed.json');
        $payload['event'] = 'unknown';

        config(['inpost.organization' => '1']);
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '91.216.25.105'])
            ->post('/inpost/webhook', $payload);

        $response->assertStatus(400);
    }

    public function test_throws_exception_if_shipment_ident_is_not_found()
    {
        config(['inpost.organization' => '1']);
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '91.216.25.100'])
            ->post('/inpost/webhook', $this->fakeRequestPayload('ShipmentConfirmed.json'));

        $response->assertStatus(404);
        $this->assertDatabaseMissing('inpost_shipment_numbers', [
            'inpost_ident' => '49',
        ]);
    }

    public function test_can_store_shipment_tracking_number_in_database()
    {
        config(['inpost.organization' => '1']);
        InPostShipmentNumber::create(['inpost_ident' => '49']);
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '91.216.25.100'])
            ->post('/inpost/webhook', $this->fakeRequestPayload('ShipmentConfirmed.json'));

        $response->assertStatus(200);

        $this->assertDatabaseHas('inpost_shipment_numbers', [
            'inpost_ident' => '49',
            'tracking_number' => '602677439331630337653846',
            'status' => 'confirmed',
        ]);
    }

    public function test_can_update_shipment_status_in_database()
    {
        config(['inpost.organization' => '1']);
        InPostShipmentNumber::create(['inpost_ident' => '49', 'status' => 'confirmed']);
        $response = $this
            ->withServerVariables(['REMOTE_ADDR' => '91.216.25.100'])
            ->post('/inpost/webhook', $this->fakeRequestPayload('ShipmentStatusChanged.json'));

        $response->assertStatus(200);

        $this->assertDatabaseHas('inpost_shipment_numbers', [
            'inpost_ident' => '49',
            'tracking_number' => '602677439331630337653846',
            'status' => 'delivered',
        ]);
    }

    public function test_status_change_dispatches_event()
    {
        Event::fake();

        config(['inpost.organization' => '1']);
        InPostShipmentNumber::create(['inpost_ident' => '49', 'status' => 'confirmed']);
        $this
            ->withServerVariables(['REMOTE_ADDR' => '91.216.25.100'])
            ->post('/inpost/webhook', $this->fakeRequestPayload('ShipmentStatusChanged.json'));

        Event::assertDispatched(InPostShipmentStatusChangedEvent::class, function($event) {
            $this->assertEquals('602677439331630337653846', $event->getTrackingNumber());
            $this->assertEquals('delivered', $event->getStatus());

            $statusDetails = $event->getStatusDetails();
            $this->assertIsArray($statusDetails);
            $this->assertArrayHasKey('name', $statusDetails);
            $this->assertNotNull($statusDetails['name']);
            $this->assertArrayHasKey('title', $statusDetails);
            $this->assertNotNull($statusDetails['title']);
            $this->assertArrayHasKey('description', $statusDetails);
            $this->assertNotNull($statusDetails['description']);
            return true;
        });
    }
}