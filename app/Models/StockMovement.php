<?php

namespace App\Models;

use App\Enums\MovementDirection;
use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'stock_movements';

    protected $fillable = [
        'movement_number',
        'movement_date',
        'warehouse_id',
        'bin_id',
        'product_id',
        'lot_id',
        'pallet_id',
        'movement_type',
        'direction',
        'quantity',
        'balance_after',
        'reference_type',
        'reference_id',
        'grn_id',
        'production_receipt_id',
        'performed_by',
        'notes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'movement_date' => 'datetime',
            'movement_type' => StockMovementType::class,
            'direction' => MovementDirection::class,
            'quantity' => 'decimal:6',
            'balance_after' => 'decimal:6',
            'created_at' => 'datetime',
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

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class, 'grn_id');
    }

    public function productionReceipt(): BelongsTo
    {
        return $this->belongsTo(ProductionReceipt::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
