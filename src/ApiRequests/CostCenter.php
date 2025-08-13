<?php

namespace Xgrz\InPost\ApiRequests;

class CostCenter extends BaseShipXCall
{
    protected string $endpoint = '';

    public function get()
    {
        $this->endpoint = '/v1/organizations/:id/mpks';

        return $this->getCall();
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
