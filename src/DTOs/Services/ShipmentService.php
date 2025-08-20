<?php

namespace Xgrz\InPost\DTOs\Services;

use Xgrz\InPost\Exceptions\ShipXException;
use Xgrz\InPost\Models\InPostService;

class ShipmentService
{
    protected array $customAttributes = [];
    protected array $additionalServices = [];
    protected ?InPostService $service = NULL;

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
        $service = InPostService::query()
            ->with('additionalServices')
            ->where('id', $serviceName)
            ->first();

        if ($service && $service->active) {
            $this->service = $service;
            $this->additionalServices = [];
        } elseif (! $service) {
            throw new ShipXException('Service not found: [' . $serviceName . ']');
        } else {
            throw new ShipXException('Service [' . $serviceName . '] is locally disabled');
        }

        return $this;
    }

    /**
     * @throws ShipXException
     */
    public function additionalService(string $additionalServiceName): static
    {
        if (! $this->service) {
            throw new ShipXException('Service not set. Use setService() method first.');
        }

        $additionalService = $this->service
            ->additionalServices
            ->filter(fn($additionalService) => $additionalService->ident === str($additionalServiceName)->lower()->toString())
            ->first();

        if ($additionalService && $additionalService->active) {
            $this->additionalServices[] = $additionalService->ident;
        } elseif (! $additionalService) {
            throw new ShipXException('Additional service not found [' . $additionalServiceName . ']');
        } else {
            throw new ShipXException('Additional service [' . $additionalServiceName . '] is locally disabled for selected ' . $this->service->id . ' service');
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
        $basePayload['service'] = $this->service?->id;
        $basePayload['additional_services'] = $this->additionalServices;
        $basePayload['custom_attributes'] = $this->customAttributes;
        return $basePayload;
    }

    public function toArray()
    {
        return [
            'service' => $this->service?->id,
            'additional_services' => $this->additionalServices,
            'target_point' => $this->customAttributes['target_point'] ?? NULL,
        ];
    }
}