<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class ProductionReceiptItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (ProductionReceiptItem $item): void {
            if ((float) $item->rejected_qty > (float) $item->received_qty) {
                throw ValidationException::withMessages([
                    'rejected_qty' => 'Rejected quantity cannot exceed received quantity.',
                ]);
            }
        });
    }

    protected $table = 'production_receipt_items';

    protected $fillable = [
        'production_receipt_id',
        'product_id',
        'uom_id',
        'lot_id',
        'pallet_id',
        'destination_bin_id',
        'received_qty',
        'rejected_qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_qty' => 'decimal:6',
            'rejected_qty' => 'decimal:6',
        ];
    }

    public function productionReceipt(): BelongsTo
    {
        return $this->belongsTo(ProductionReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(Uom::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function pallet(): BelongsTo
    {
        return $this->belongsTo(Pallet::class);
    }

    public function destinationBin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_bin_id');
    }

    public function putawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class);
    }
}
