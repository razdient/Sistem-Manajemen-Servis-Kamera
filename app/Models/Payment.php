<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const TYPE_DP = 'dp';

    public const TYPE_PARTIAL = 'partial';

    public const TYPE_INSTALLMENT = 'installment';

    public const TYPE_FULL = 'full';

    public const TYPE_LABELS = [
        self::TYPE_DP => 'DP',
        self::TYPE_PARTIAL => 'Pembayaran Sebagian',
        self::TYPE_INSTALLMENT => 'Cicilan',
        self::TYPE_FULL => 'Pelunasan',
    ];

    public const METHOD_CASH = 'cash';

    public const METHOD_TRANSFER = 'transfer';

    public const METHOD_QRIS = 'qris';

    public const METHOD_OTHER = 'other';

    public const METHOD_LABELS = [
        self::METHOD_CASH => 'Tunai',
        self::METHOD_TRANSFER => 'Transfer',
        self::METHOD_QRIS => 'QRIS',
        self::METHOD_OTHER => 'Lainnya',
    ];

    protected $fillable = [
        'service_visit_id',
        'amount',
        'payment_type',
        'payment_method',
        'paid_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function serviceVisit(): BelongsTo
    {
        return $this->belongsTo(ServiceVisit::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->payment_type] ?? $this->payment_type;
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHOD_LABELS[$this->payment_method] ?? $this->payment_method;
    }

    public function getFormattedAmountAttribute(): string
    {
        return ServiceItem::formatRupiah($this->amount);
    }
}
