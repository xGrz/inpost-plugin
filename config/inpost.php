<?php

return [
    'url' => env('INPOST_API_URL'),
    'token' => env('INPOST_API_TOKEN'),
    'organization' => env('INPOST_API_ORGANIZATION', ''),
    'widget' => env('INPOST_API_GEOWIDGET', ''),
    'minimum_insurance_value' => 500,
    'synchronize_points_chunk_size' => 500,
    'cache' => [
        'statuses' => [
            'key' => 'inpost-statuses',
            'ttl' => 86400,
        ],
    ],
    // InPost allow do download 'normal' or 'A6' label
    'label_type' => 'A6',
    // InPost allow do download 'pdf', 'zpl' or 'epl' label
    'label_format' => 'pdf',
    'webhook_ip_restriction' => '91.216.25.0/24',
];
