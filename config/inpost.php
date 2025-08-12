<?php

return [
    'url' => env('INPOST_API_URL'),
    'token' => env('INPOST_API_TOKEN'),
    'organization' => env('INPOST_API_ORGANIZATION', ''),
    'widget' => env('INPOST_API_GEOWIDGET', ''),
    'minimum_insurance_value' => 500,
    'synchronize_points_chunk_size' => 500,
];
