<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Wezlo\FilamentApproval\Concerns\HasApprovals;

class PurchaseOrder extends Model
{
    use SoftDeletes, HasFactory, LogsActivity, HasApprovals;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'document_number',
        'order_date',
        'expected_date',
        'status',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_date' => 'date',
            'status' => PurchaseOrderStatus::class,
            'approved_at' => 'datetime',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function onApprovalSubmitted(): void
    {
        $this->update(['status' => PurchaseOrderStatus::SUBMITTED]);
    }

    public function onApprovalApproved(): void
    {
        $this->update([
            'status' => PurchaseOrderStatus::APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
    }

    public function onApprovalRejected(): void
    {
        $this->update([
            'status' => PurchaseOrderStatus::DRAFT,
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
            ->useLogName('purchase_order');
    }
}
