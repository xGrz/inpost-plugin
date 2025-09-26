# Parcel definitions

Parcel templates are used to define parcels for shipments.

## Parcel Locker Template

Defined for parcel locker shipments. Possible values:

```php
Xgrz\Inpost\Enums\InPostParcelLocker::S;
Xgrz\Inpost\Enums\InPostParcelLocker::M;
Xgrz\Inpost\Enums\InPostParcelLocker::L;
Xgrz\Inpost\Enums\InPostParcelLocker::XL;
```
You can use them also as templates for Courier shipments.


## Parcel from stored templates (only courier shipments)

Use the following model:
```php
\App\Models\ParcelTemplate::class;
```
Create your own templates describing parcel size/weight and assign them to the parcel.



## Custom parcel (only courier shipments)
One time parcel can be defined as

```php
use Xgrz\InPost\ShipmentComponents\Parcels\InPostParcel;

InPostParcel::make(
    <int:$width>, 
    <int:$height>, 
    <int:$length>, 
    <float:$weight>, 
    <int:$quantity>, 
    <bool:$non_standard>
);
```