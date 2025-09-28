# Parcels

## Parcels Locker Template

Defined for parcel locker shipments. Possible values:

```php
Xgrz\Inpost\Enums\InPostParcelLocker::S;
Xgrz\Inpost\Enums\InPostParcelLocker::M;
Xgrz\Inpost\Enums\InPostParcelLocker::L;
Xgrz\Inpost\Enums\InPostParcelLocker::XL;
```

## Custom parcel (only courier shipments)
One time parcel can be defined as

```php

\Xgrz\InPost\ShipmentComponents\Parcels\InPostParcel::make(
    <int:$width>, 
    <int:$height>, 
    <int:$length>, 
    <float:$weight>, 
    <int:$quantity>, 
    <bool:$non_standard>
);
```

## Add parcel to shipment

```php
$s = new \Xgrz\InPost\Facades\InPostShipment();
$s->parcels->add(\Xgrz\InPost\Enums\InPostParcelLocker|\Xgrz\InPost\ShipmentComponents\Parcels\InPostParcel)
```

or shorthand
```php
$s = new \Xgrz\InPost\Facades\InPostShipment();
$s->parcel(\Xgrz\InPost\Enums\InPostParcelLocker|\Xgrz\InPost\ShipmentComponents\Parcels\InPostParcel)
```
