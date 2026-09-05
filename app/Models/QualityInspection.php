<?php

namespace App\Models;

use App\Enums\QcStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Wezlo\FilamentApproval\Concerns\HasApprovals;

class QualityInspection extends Model
{
    use HasFactory, LogsActivity, HasApprovals;

    protected $table = 'quality_inspections';

    protected $fillable = [
        'goods_receipt_id',
        'production_receipt_id',
        'inspection_number',
        'inspection_date',
        'status',
        'inspected_by',
        'approved_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'datetime',
            'status' => QcStatus::class,
        ];
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function productionReceipt(): BelongsTo
    {
        return $this->belongsTo(ProductionReceipt::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QualityInspectionItem::class);
    }

    public function onApprovalSubmitted(): void
    {
        $this->update(['status' => QcStatus::PENDING]);
    }

    public function onApprovalApproved(): void
    {
        $this->update([
            'status' => $this->items()->where('failed_qty', '>', 0)->exists()
                ? QcStatus::PARTIALLY_PASSED
                : QcStatus::PASSED,
            'approved_by' => Auth::id(),
        ]);
    }

    public function onApprovalRejected(): void
    {
        $this->update([
            'status' => QcStatus::FAILED,
            'approved_by' => null,
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('quality_inspection');
    }
}
