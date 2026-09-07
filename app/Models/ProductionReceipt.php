<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Wezlo\FilamentApproval\Concerns\HasApprovals;

class ProductionReceipt extends Model
{
    use HasFactory, LogsActivity, HasApprovals;

    protected $table = 'production_receipts';

    protected $fillable = [
        'document_number',
        'production_order_id',
        'warehouse_id',
        'receipt_date',
        'status',
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
        return $this->hasMany(ProductionReceiptItem::class);
    }

    public function qualityInspections(): HasMany
    {
        return $this->hasMany(QualityInspection::class);
    }

    public function putawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function onApprovalSubmitted(): void
    {
        $this->update(['status' => DocumentStatus::SUBMITTED]);
    }

    public function onApprovalApproved(): void
    {
        app(\App\Services\InboundReceivingService::class)
            ->confirmProductionReceipt($this, Auth::id());
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
            ->useLogName('production_receipt');
    }
}
