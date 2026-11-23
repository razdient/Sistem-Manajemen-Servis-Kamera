<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceVisit extends Model
{
    use SoftDeletes;

    public const ESTIMATE_NOT_STARTED = 'not_started';

    public const ESTIMATE_WAITING_APPROVAL = 'waiting_approval';

    public const ESTIMATE_APPROVED = 'approved';

    public const ESTIMATE_REJECTED = 'rejected';

    public const ESTIMATE_STATUS_LABELS = [
        self::ESTIMATE_NOT_STARTED => 'Belum Diperiksa',
        self::ESTIMATE_WAITING_APPROVAL => 'Menunggu Persetujuan',
        self::ESTIMATE_APPROVED => 'Disetujui',
        self::ESTIMATE_REJECTED => 'Ditolak',
    ];

    protected $fillable = [
        'customer_id',
        'service_number',
        'notes',
        'estimate_status',
        'estimated_at',
        'decision_at',
        'ready_at',
        'warranty_started_at',
        'warranty_ended_at',
    ];

    protected function casts(): array
    {
        return [
            'estimated_at' => 'datetime',
            'decision_at' => 'datetime',
            'ready_at' => 'datetime',
            'warranty_started_at' => 'datetime',
            'warranty_ended_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        // Pelanggan yang sudah diarsipkan tetap ditampilkan pada riwayat servis.
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function devices(): HasMany
    {
        return $this->hasMany(ServiceDevice::class);
    }

    public function items(): HasManyThrough
    {
        return $this->hasManyThrough(
            ServiceItem::class,
            ServiceDevice::class,
            'service_visit_id',
            'service_device_id',
            'id',
            'id',
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('paid_at');
    }

    public static function estimateStatusLabel(?string $status): string
    {
        return self::ESTIMATE_STATUS_LABELS[$status] ?? ($status ?: '-');
    }

    public function getEstimateStatusLabelAttribute(): string
    {
        return self::estimateStatusLabel($this->estimate_status);
    }

    public function getEstimateStatusBadgeClassAttribute(): string
    {
        return match ($this->estimate_status) {
            self::ESTIMATE_WAITING_APPROVAL => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
            self::ESTIMATE_APPROVED => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            self::ESTIMATE_REJECTED => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            default => 'bg-surface-secondary text-muted',
        };
    }

    public function getEstimateTotalAttribute(): int
    {
        return $this->items->sum(fn (ServiceItem $item) => $item->subtotal);
    }

    public function getEstimateCostTotalAttribute(): int
    {
        return $this->items->sum(fn (ServiceItem $item) => $item->total_cost);
    }

    public function getFormattedEstimateTotalAttribute(): string
    {
        return ServiceItem::formatRupiah($this->estimate_total);
    }

    public function getFormattedEstimateCostTotalAttribute(): string
    {
        return ServiceItem::formatRupiah($this->estimate_cost_total);
    }

    public function isEstimateDecided(): bool
    {
        return in_array($this->estimate_status, [
            self::ESTIMATE_APPROVED,
            self::ESTIMATE_REJECTED,
        ], true);
    }

    public function isEstimateEditable(): bool
    {
        return ! $this->isEstimateDecided();
    }

    public function getTotalPaidAttribute(): int
    {
        if ($this->relationLoaded('payments')) {
            return (int) $this->payments->sum('amount');
        }

        return (int) $this->payments()->sum('amount');
    }

    public function getRemainingBalanceAttribute(): int
    {
        $remaining = $this->estimate_total - $this->total_paid;

        return $remaining > 0 ? $remaining : 0;
    }

    public function getFormattedTotalPaidAttribute(): string
    {
        return ServiceItem::formatRupiah($this->total_paid);
    }

    public function getFormattedRemainingBalanceAttribute(): string
    {
        return ServiceItem::formatRupiah($this->remaining_balance);
    }

    public function isPaidOff(): bool
    {
        return $this->estimate_total > 0 && $this->remaining_balance === 0;
    }

    public function allDevicesCompleted(): bool
    {
        $devices = $this->relationLoaded('devices') ? $this->devices : $this->devices()->get();

        return $devices->isNotEmpty()
            && $devices->every(fn (ServiceDevice $device) => $device->status === ServiceDevice::STATUS_COMPLETED);
    }

    public function syncWarranty(): void
    {
        if ($this->allDevicesCompleted()) {
            if ($this->ready_at) {
                return;
            }

            $readyAt = now();

            $this->update([
                'ready_at' => $readyAt,
                'warranty_started_at' => $readyAt,
                'warranty_ended_at' => $readyAt->copy()->addMonthNoOverflow(),
            ]);

            return;
        }

        if ($this->ready_at || $this->warranty_started_at || $this->warranty_ended_at) {
            $this->update([
                'ready_at' => null,
                'warranty_started_at' => null,
                'warranty_ended_at' => null,
            ]);
        }
    }

    public function getStatusLabelsAttribute(): string
    {
        return $this->devices
            ->pluck('status')
            ->unique()
            ->map(fn (?string $status) => ServiceDevice::statusLabel($status))
            ->implode(', ');
    }
}
