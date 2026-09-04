<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Wezlo\FilamentApproval\Concerns\HasApprovals;

class GoodsReceipt extends Model
{
    use HasFactory, LogsActivity, HasApprovals;

    protected $table = 'goods_receipts';

    protected $fillable = [
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'document_number',
        'receipt_date',
        'status',
        'delivery_note_number',
        'received_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'receipt_date' => 'datetime',
            'status' => DocumentStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function onApprovalSubmitted(): void
    {
        $this->update(['status' => DocumentStatus::SUBMITTED]);
    }

    public function onApprovalApproved(): void
    {
        $this->update([
            'status' => DocumentStatus::APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    public function onApprovalRejected(): void
    {
        $this->update([
            'status' => DocumentStatus::REJECTED,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('goods_receipt');
    }
}
