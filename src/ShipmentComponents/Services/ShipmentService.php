<?php

namespace Xgrz\InPost\ShipmentComponents\Services;

use Xgrz\InPost\Exceptions\ShipXException;
use Xgrz\InPost\Facades\InPost;

class ShipmentService
{
    protected array $customAttributes = [];
    protected array $additionalServices = [];
    protected ?string $service = NULL;

    public function __construct()
    {
    }

    public static function make(): static
    {
        return new static();
    }

    /**
     * @throws ShipXException
     */
    public function setService(string $serviceName): static
    {
        $service = InPost::services()->get($serviceName);

        if ($service) {
            $this->service = $service['id'];
            $this->additionalServices = [];
        } else {
            throw new ShipXException('Service not found: [' . $serviceName . ']');
        }

        return $this;
    }

    /**
     * @throws ShipXException
     */
    public function additionalServices(string|array $additionalServiceNames): static
    {
        if (! $this->service) {
            throw new ShipXException('Service not set. Use setService() method first.');
        }

        if (is_string($additionalServiceNames)) {
            $additionalServiceNames = [$additionalServiceNames];
        }

        $additionalServices = collect(InPost::services()->get($this->service)['additional_services'])
            ->keyBy('id');

        foreach ($additionalServiceNames as $additionalServiceName) {
            if ($additionalServices->has(strtolower($additionalServiceName))) {
                $this->additionalServices[] = strtolower($additionalServiceName);
            } else {
                throw new ShipXException('Additional service not found [' . $additionalServiceName . ']');
            }
        }

        return $this;
    }

    public function targetPoint(?string $targetPoint): static
    {
        if ($targetPoint) {
            $this->customAttributes['target_point'] = $targetPoint;
        }

        return $this;
    }

    public function payload(array $basePayload = []): array
    {
        $basePayload['service'] = $this->service;
        $basePayload['additional_services'] = $this->additionalServices;
        $basePayload['custom_attributes'] = $this->customAttributes;
        return $basePayload;
    }

    public function toArray()
    {
        return [
            'service' => $this->service,
            'additional_services' => $this->additionalServices,
            'target_point' => $this->customAttributes['target_point'] ?? NULL,
        ];
    }
}