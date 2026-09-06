<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (GoodsReceiptItem $item): void {
            $qualityCheckedQty = (float) $item->accepted_qty + (float) $item->rejected_qty;

            if ($qualityCheckedQty > (float) $item->received_qty) {
                throw ValidationException::withMessages([
                    'accepted_qty' => 'Accepted and rejected quantities cannot exceed received quantity.',
                    'rejected_qty' => 'Accepted and rejected quantities cannot exceed received quantity.',
                ]);
            }
        });
    }

    protected $table = 'goods_receipt_items';

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_item_id',
        'product_id',
        'uom_id',
        'lot_id',
        'pallet_id',
        'received_qty',
        'accepted_qty',
        'rejected_qty',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_qty' => 'decimal:6',
            'accepted_qty' => 'decimal:6',
            'rejected_qty' => 'decimal:6',
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
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

    public function putawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class);
    }
}
