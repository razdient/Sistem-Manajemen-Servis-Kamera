<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    public const TYPE_LABELS = ServiceItem::TYPE_LABELS;

    protected $fillable = [
        'item_type',
        'category',
        'name',
        'default_price',
        'default_cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'integer',
            'default_cost' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public static function typeLabel(?string $type): string
    {
        return ServiceItem::typeLabel($type);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabel($this->item_type);
    }

    public function getFormattedDefaultPriceAttribute(): string
    {
        return ServiceItem::formatRupiah($this->default_price);
    }

    public function getFormattedDefaultCostAttribute(): string
    {
        return ServiceItem::formatRupiah($this->default_cost);
    }
}
