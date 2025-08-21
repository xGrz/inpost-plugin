# Parcel definitions

Parcel templates are used to define parcels for shipments.

## Parcel Locker Template

Defined for parcel locker shipments. Possible values:

```php
Xgrz\Inpost\Enums\ParcelLockerTemplate::S;
Xgrz\Inpost\Enums\ParcelLockerTemplate::M;
Xgrz\Inpost\Enums\ParcelLockerTemplate::L;
Xgrz\Inpost\Enums\ParcelLockerTemplate::XL;
```
You can use them also as templates for Courier shipments.


## Parcel from stored templates (only courier shipments)

Use the following model:
```php
\Xgrz\InPost\Models\ParcelTemplate::class;
```
Create your own templates describing parcel size/weight and assign them to the parcel.



## Custom parcel (only courier shipments)
One time parcel can be defined as
```php
use Xgrz\InPost\ShipmentComponents\Parcels\CourierParcel;

CourierParcel::make(
    <int:$width>, 
    <int:$height>, 
    <int:$length>, 
    <float:$weight>, 
    <int:$quantity>, 
    <bool:$non_standard>
);
```