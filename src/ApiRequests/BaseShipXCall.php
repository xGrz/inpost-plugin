<?php

namespace Xgrz\InPost\ApiRequests;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Xgrz\InPost\Config\InPostConfig;
use Xgrz\InPost\Exceptions\ShipXConfigurationException;
use Xgrz\InPost\Exceptions\ShipXException;

abstract class BaseShipXCall
{
    protected string $endpoint;
    protected array $payload = [];
    protected Collection $props;

    public function __construct()
    {
        $this->props = collect();
    }

    /**
     * @throws ShipXConfigurationException
     */
    protected function baseCall(): PendingRequest|Factory
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . InPostConfig::token(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Accept-Language' => 'pl-PL',
        ]);
    }

    /**
     * @throws ShipXException
     */
    protected function getEndpoint(): string
    {
        $endpoint = str($this->endpoint)
            ->replaceEnd(':id', InPostConfig::organizationId())
            ->replace(':id/', InPostConfig::organizationId() . '/')
            ->when(
                $this->props->isNotEmpty(),
                fn($endpoint) => $endpoint
                    ->replace(
                        $this->props->keys()->toArray(),
                        $this->props->values()->toArray()
                    )
            );

        if ($endpoint->contains(':')) {
            $missing = $endpoint->matchAll('/:([^\/\s]+)(?=\/|$)/')->join(', ');
            throw new ShipXException('Missing prop for endpoint: [' . $missing . ']');
        }
        return $endpoint;
    }

    protected function setProp(string $key, mixed $value): static
    {
        $key = str($key)->startsWith(':')
            ? $key
            : str($key)->prepend(':')->toString();
        $this->props->put($key, $value);
        return $this;
    }

    /**
     * @throws ShipXException
     * @throws ShipXConfigurationException
     */
    protected function getFullEndpoint(): string
    {
        return InPostConfig::baseUrl() . $this->getEndpoint();
    }

    public function buildCommaSeparatedPayload(): array
    {
        return collect($this->payload)
            ->map(fn($value) => is_array($value) ? implode(',', $value) : $value)
            ->toArray();
    }

    /**
     * @throws ConnectionException
     * @throws ShipXConfigurationException
     * @throws ShipXException
     */
    protected function getCall()
    {
        return $this
            ->baseCall()
            ->withQueryParameters($this->buildCommaSeparatedPayload())
            ->get($this->getFullEndpoint())
            ->json();
    }

    /**
     * @throws ConnectionException
     * @throws ShipXConfigurationException
     * @throws ShipXException
     */
    protected function getFile()
    {
        return $this
            ->baseCall()
            ->get($this->getFullEndpoint())
            ->getBody()
            ->getContents();
    }

    /**
     * @throws ConnectionException
     * @throws ShipXConfigurationException
     * @throws ShipXException
     */
    protected function postCall()
    {
        return $this
            ->baseCall()
            ->post($this->getFullEndpoint(), $this->payload)
            ->json();
    }

    /**
     * @throws ConnectionException
     * @throws ShipXConfigurationException
     * @throws ShipXException
     */
    protected function putCall()
    {
        return $this
            ->baseCall()
            ->put($this->getFullEndpoint(), $this->payload)
            ->json();
    }


}
