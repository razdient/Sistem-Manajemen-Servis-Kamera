<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'expense_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'expense_date' => 'date',
        ];
    }

    public function getFormattedAmountAttribute(): string
    {
        return ServiceItem::formatRupiah($this->amount);
    }
}
