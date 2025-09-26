# Shipment

## Create a shipment instance

```php
$shipment = new Xgrz\InPost\Facades\InPostShipment();
```

## 1. Fill sender data

```php
$shipment->sender->phone = '123456789';
$shipment->sender->email = 'inpost@inpost.pl';
$shipment->sender->company_name = 'Company';
$shipment->sender->name = '';
$shipment->sender->street = 'Street';
$shipment->sender->building_number = '123';
$shipment->sender->post_code = '12-345';
$shipment->sender->city = 'City';
$shipment->sender->country_code = 'PL'; // optional - default is PL
```

If sender data is not set, it will be fetched from the InPost account.

## 2. Fill receiver data

### ParcelLocker shipments

```php
$shipment->receiver->phone = '123456789';
$shipment->receiver->email = 'inpost@inpost.pl';

// optional
$shipment->receiver->company_name = 'Company'; // optional for ParcelLocker shipments
$shipment->receiver->name = ''; // optional for ParcelLocker shipments
```

### Delivery to customer address

```php
$shipment->receiver->company_name = 'Company';
$shipment->receiver->name = '';
$shipment->receiver->street = 'Street';
$shipment->receiver->building_number = '123';
$shipment->receiver->post_code = '12-345';
$shipment->receiver->city = 'City';
$shipment->receiver->country_code = 'PL'; // optional - default is PL

$shipment->receiver->phone = '123456789'; // optional 
$shipment->receiver->email = 'inpost@inpost.pl'; // optional
```

## 3. Add parcels
From Parcel locker template

```php
$shipment->parcels->add(\Xgrz\InPost\Enums\InPostParcelLocker::L);
```

Custom parcel
```php
$shipment->parcels->add(width: 40, height: 30, length: 20, quantity: 1, non_standard: false); // optional
```

## 4. Insurance and cod

```php
$shipment->insurance->set(100, 'PLN'); // optional
$shipment->cod->set(100); // optional
```

## 5. Reference / comment

```php
$shipment->comment('Comment'); // optional
$shipment->reference('Reference'); // optional
```

## 6. Send