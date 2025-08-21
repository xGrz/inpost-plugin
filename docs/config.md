# Config

You can config inpost plugin in `config/inpost.php`

Default config:
```php
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
    'webhook_url' => env('INPOST_WEBHOOK_URL', '/inpost-webhook'),
```

## .env

Organization, token and geowidget tokens should be secret.
For safety reasons, you have to add into your .env:

```dotenv
INPOST_API_URL=
INPOST_API_TOKEN=
INPOST_API_ORGANIZATION=
INPOST_API_GEOWIDGET=
```

All values you can generate with your account on https://inpost.pl/ (or https://sandbox-manager.paczkomaty.pl/ for sandbox)

* Production environment: https://api-shipx-pl.easypack24.net
* Development environment: https://sandbox-manager.paczkomaty.pl/