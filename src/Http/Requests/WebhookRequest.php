<?php

namespace Xgrz\InPost\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class WebhookRequest extends FormRequest
{
    protected array $allowedEvents = [
        'shipment_confirmed',
        'offers_prepared',
        'shipment_status_changed',
    ];

    public function rules(): array
    {
        return [
            'event_ts' => 'required|date',
            'event' => 'required|string',
            'payload' => 'required|array',
            'organization_id' => 'required|numeric',
        ];
    }

    public function authorize(): bool
    {
        return $this->request->has('organization_id')
            && $this->request->get('organization_id') == config('inpost.organization');
    }

    public function webhookEvent(): ?string
    {
        $event = $this->validated('event');
        if (! in_array($event, $this->allowedEvents)) {
            Log::emergency('Unknown event: ' . $event, $this->request->all());
            return NULL;
        }
        return $this->request->get('event');
    }

    public function webhookPayload(): array
    {
        $payload = $this->validated('payload') ?? [];
        return is_array($payload) ? $payload : [];
    }

    public function webhookTimestamp(): Carbon
    {
        return Carbon::make($this->validated('event_ts'));
    }

}
