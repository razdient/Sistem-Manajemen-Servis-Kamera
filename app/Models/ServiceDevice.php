<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceDevice extends Model
{
    use SoftDeletes;

    public const STATUS_RECEIVED = 'received';

    public const STATUS_INSPECTING = 'inspecting';

    public const STATUS_WAITING_APPROVAL = 'waiting_approval';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_WAITING_PARTS = 'waiting_parts';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_LABELS = [
        self::STATUS_RECEIVED => 'Diterima',
        self::STATUS_INSPECTING => 'Sedang Diperiksa',
        self::STATUS_WAITING_APPROVAL => 'Menunggu Persetujuan',
        self::STATUS_IN_PROGRESS => 'Sedang Dikerjakan',
        self::STATUS_WAITING_PARTS => 'Menunggu Suku Cadang',
        self::STATUS_COMPLETED => 'Selesai',
        self::STATUS_CANCELLED => 'Tidak Jadi Diperbaiki',
    ];

    protected $fillable = [
        'service_visit_id',
        'device_type',
        'brand',
        'model',
        'complaint',
        'physical_condition',
        'accessories',
        'status',
    ];

    public static function statusLabel(?string $status): string
    {
        return self::STATUS_LABELS[$status] ?? ($status ?: '-');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabel($this->status);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_RECEIVED => 'bg-surface-secondary text-muted',
            self::STATUS_INSPECTING => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            self::STATUS_WAITING_APPROVAL => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            self::STATUS_IN_PROGRESS => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
            self::STATUS_WAITING_PARTS => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            default => 'bg-surface-secondary text-muted',
        };
    }

    public function serviceVisit(): BelongsTo
    {
        return $this->belongsTo(ServiceVisit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(ServiceHistory::class)->latest();
    }
}
