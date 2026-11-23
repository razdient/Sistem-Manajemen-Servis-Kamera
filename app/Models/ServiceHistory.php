<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceHistory extends Model
{
    protected $fillable = [
        'service_device_id',
        'old_status',
        'new_status',
        'note',
    ];

    public function serviceDevice(): BelongsTo
    {
        return $this->belongsTo(ServiceDevice::class);
    }

    public function getOldStatusLabelAttribute(): string
    {
        return $this->old_status ? ServiceDevice::statusLabel($this->old_status) : '-';
    }

    public function getNewStatusLabelAttribute(): string
    {
        return ServiceDevice::statusLabel($this->new_status);
    }
}
