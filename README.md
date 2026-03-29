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
* [Artisan commands](docs/commands.md) (local cache for inpost points)


## InPost points of delivery and locker synchronization

You should at least once a day to synchronize points of delivery and lockers.

You can do that by running:

```bash
php artisan inpost:points
```

or better, by setting up a cron action in scheduler:
```php
\Xgrz\InPost\Actions\SynchronizePointsAction::dispatch();
```
As a parameter you can pass the queue name for jobs. This Action dispatches jobs to the queue.

The first run can generate a lot of jobs. After initial synchronization, only changes will be processed.