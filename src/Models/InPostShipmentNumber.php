<?php

namespace Xgrz\InPost\Models;

use Illuminate\Database\Eloquent\Model;

class InPostShipmentNumber extends Model
{
    protected $primaryKey = 'inpost_ident';
    protected $keyType = 'string';
    protected $table = 'inpost_shipment_numbers';
    protected $guarded = [];
}
