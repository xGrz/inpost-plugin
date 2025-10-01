# Laravel InPost plugin (ShipX v1)
Requirements: Laravel 11+, PHP 8.2+, queue, cache, database

## Installation

In your project composer.json, in a repositories section add:
```json
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:xGrz/inpost-plugin.git"
    }
]
```

Then run:
```commandline
composer require xgrz/inpost-plugin
```

```commandline
php artisan inpost:publish-config
php artisan inpost:publish-migrations
php artisan migrate 
```

## Sections

* [Package configuration](docs/config.md)
* [InPost Point Searcher](docs/point-searcher.md)
* [Parcels](docs/parcels.md)
* [Webhook](docs/webhook.md)
* [Shipment](docs/shipment.md)
* [Tracking](docs/tracking.md)
* [Events](docs/events.md)

