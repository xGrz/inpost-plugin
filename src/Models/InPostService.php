<?php

namespace Xgrz\InPost\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Xgrz\InPost\Observers\InPostServiceObserver;

#[ObservedBy([InPostServiceObserver::class])]
class InPostService extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    protected $table = 'inpost_services';
    protected $guarded = [];
    protected $casts = [
        'active' => 'boolean',
    ];

    public function additionalServices(): HasMany
    {
        return $this->hasMany(InPostAdditionalService::class, 'inpost_service_id');
    }
}
