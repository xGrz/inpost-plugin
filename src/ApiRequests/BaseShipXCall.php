<?php

namespace Xgrz\InPost\ApiRequests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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

    protected static function token(): string
    {
        return config('inpost.token', '');
    }

    protected static function baseUrl(): string
    {
        return config('inpost.url', '');
    }

    protected static function organizationId(): int
    {
        $organizationId = config('inpost.organization');
        if (empty($organizationId)) throw new ShipXException('Missing organization id');
        return config('inpost.organization');
    }

    protected function baseCall(): PendingRequest|Factory
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . self::token(),
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
            ->replaceEnd(':id', self::organizationId())
            ->replace(':id/', self::organizationId() . '/')
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

    protected function getFullEndpoint(): string
    {
        return self::baseUrl() . $this->getEndpoint();
    }

    protected function getCall()
    {

        return $this
            ->baseCall()
            ->withQueryParameters($this->payload)
            ->get($this->getFullEndpoint())
            ->json();
    }

    protected function getFile()
    {
        return $this
            ->baseCall()
            ->get($this->getFullEndpoint())
            ->getBody()
            ->getContents();
    }

    protected function postCall()
    {
        return $this
            ->baseCall()
            ->post($this->getFullEndpoint(), $this->payload)
            ->json();
    }

    protected function putCall()
    {
        return $this
            ->baseCall()
            ->put($this->getFullEndpoint(), $this->payload)
            ->json();
    }


}
