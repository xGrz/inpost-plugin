<?php

namespace Xgrz\InPost\ApiRequests;

use Illuminate\Support\Collection;

class CostCenter extends BaseShipXCall
{
    protected string $endpoint = '';

    public function get(): Collection
    {
        $this->endpoint = '/v1/organizations/:id/mpks';
        $response = $this->getCall();
        return array_key_exists('items', $response)
            ? collect($response['items'])
            : collect([]);
    }

    public function details(int $inpostId)
    {
        $this->endpoint = '/v1/mpks/:mpk_id';
        $this->setProp('mpk_id', $inpostId);

        return $this->getCall();
    }

    public function create(string $name, string $description)
    {
        $this->endpoint = '/v1/organizations/:id/mpks';

        $this->payload = [
            'name' => $name,
            'description' => $description,
        ];

        return $this->postCall();
    }

    public function update(string $name, string $description, int $inpostId)
    {
        $this->endpoint = '/v1/mpks/:mpk_id';
        $this->setProp('mpk_id', $inpostId);

        $this->payload = [
            'name' => $name,
            'description' => $description,
        ];

        return $this->putCall();
    }
}
