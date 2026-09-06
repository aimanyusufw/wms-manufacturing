<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PutawayTask extends Model
{
    use HasFactory, LogsActivity;

    protected static function booted(): void
    {
        static::saving(function (PutawayTask $task): void {
            if ($task->source_bin_id && $task->source_bin_id === $task->destination_bin_id) {
                throw ValidationException::withMessages([
                    'destination_bin_id' => 'Source and destination bins must be different.',
                ]);
            }

            $goodsReceiptItem = GoodsReceiptItem::with('goodsReceipt')
                ->find($task->goods_receipt_item_id);

            if ($goodsReceiptItem && (float) $task->qty > (float) $goodsReceiptItem->accepted_qty) {
                throw ValidationException::withMessages([
                    'qty' => 'Putaway quantity cannot exceed the accepted quantity from the goods receipt.',
                ]);
            }

            $destinationBin = $task->destinationBin()->first();

            if ($goodsReceiptItem && $destinationBin?->warehouse_id !== $goodsReceiptItem->goodsReceipt?->warehouse_id) {
                throw ValidationException::withMessages([
                    'destination_bin_id' => 'Destination bin must belong to the goods receipt warehouse.',
                ]);
            }

            if ($task->status === DocumentStatus::COMPLETED) {
                $task->completed_by ??= Auth::id();
                $task->completed_at ??= now();
            }
        });
    }

    protected $table = 'putaway_tasks';

    protected $fillable = [
        'goods_receipt_id',
        'goods_receipt_item_id',
        'product_id',
        'lot_id',
        'pallet_id',
        'source_bin_id',
        'destination_bin_id',
        'qty',
        'status',
        'assigned_to',
        'completed_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'decimal:6',
            'status' => DocumentStatus::class,
            'completed_at' => 'datetime',
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
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

    public function sourceBin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'source_bin_id');
    }

    public function destinationBin(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'destination_bin_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('putaway_task');
    }
}
