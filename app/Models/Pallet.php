<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pallet extends Model
{
    use HasFactory;

    protected $table = 'pallets';

    protected $fillable = [
        'pallet_code',
        'pallet_type',
        'weight',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:6',
        ];
    }

    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }
}
