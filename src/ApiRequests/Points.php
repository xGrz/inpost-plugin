<?php

namespace Xgrz\InPost\ApiRequests;


class Points extends BaseShipXCall
{
    protected string $endpoint = '/v1/points';

    public function get(array $search = [])
    {
        $search = collect($search)->only([
            'name',
            'type',
            'functions',
            'partner_id',
            'payment_available',
            'post_code',
            'location_247',
            'updated_from',
            'updated_to',
            'relative_point',
            'relative_post_code',
            'max_distance',
            'limit',
            'page',
            'per_page',
            'fields'
        ]);

        $this->payload = $search->toArray();

        return $this->getCall();
    }

}
