<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceItem extends Model
{
    public const TYPE_SERVICE = 'service';

    public const TYPE_PART = 'part';

    public const TYPE_LABELS = [
        self::TYPE_SERVICE => 'Jasa',
        self::TYPE_PART => 'Suku Cadang',
    ];

    protected $fillable = [
        'service_device_id',
        'item_type',
        'description',
        'quantity',
        'unit_price',
        'unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'unit_price' => 'integer',
            'unit_cost' => 'integer',
        ];
    }

    public static function typeLabel(?string $type): string
    {
        return self::TYPE_LABELS[$type] ?? ($type ?: '-');
    }

    public static function formatRupiah(int $value): string
    {
        return 'Rp '.number_format($value, 0, ',', '.');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabel($this->item_type);
    }

    public function getSubtotalAttribute(): int
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTotalCostAttribute(): int
    {
        return $this->quantity * $this->unit_cost;
    }

    public function getFormattedUnitPriceAttribute(): string
    {
        return self::formatRupiah($this->unit_price);
    }

    public function getFormattedUnitCostAttribute(): string
    {
        return self::formatRupiah($this->unit_cost);
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return self::formatRupiah($this->subtotal);
    }

    public function getFormattedTotalCostAttribute(): string
    {
        return self::formatRupiah($this->total_cost);
    }

    public function serviceDevice(): BelongsTo
    {
        return $this->belongsTo(ServiceDevice::class);
    }
}
