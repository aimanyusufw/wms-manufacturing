<?php

namespace App\Models;

use App\Enums\InventoryStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryBalance extends Model
{
    use HasFactory;

    protected $table = 'inventory_balances';

    protected $fillable = [
        'warehouse_id',
        'bin_id',
        'product_id',
        'lot_id',
        'pallet_id',
        'inventory_status',
        'quantity',
        'reserved_quantity',
        'last_movement_at',
    ];

    protected function casts(): array
    {
        return [
            'inventory_status' => InventoryStatus::class,
            'quantity' => 'decimal:6',
            'reserved_quantity' => 'decimal:6',
            'last_movement_at' => 'datetime',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'bin_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }
}
