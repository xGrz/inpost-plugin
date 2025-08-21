# Tracking shipments

## Manual tracking

There is a helper method for manual tracking in facade:
```php
Xgrz\InPost\Facades\Inpost::trackingInfo('1234567890');
```

or you can receive only tracking events:

```php
Xgrz\InPost\Facades\Inpost::trackingEvents('1234567890');
```

In case of missing shipment, an error will be thrown.
API should return events for shipments not older than 45 days.