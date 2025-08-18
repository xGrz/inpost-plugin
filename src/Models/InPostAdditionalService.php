<?php

namespace Xgrz\InPost\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InPostAdditionalService extends Model
{
    use SoftDeletes;

    protected $table = 'inpost_additional_services';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function inpostService(): BelongsTo
    {
        return $this->belongsTo(InPostService::class, 'inpost_service_id');
    }

}
