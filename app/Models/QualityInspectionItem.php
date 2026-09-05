<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class QualityInspectionItem extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::saving(function (QualityInspectionItem $item): void {
            if ((float) $item->passed_qty + (float) $item->failed_qty > (float) $item->inspected_qty) {
                throw ValidationException::withMessages([
                    'passed_qty' => 'Passed and failed quantities cannot exceed inspected quantity.',
                    'failed_qty' => 'Passed and failed quantities cannot exceed inspected quantity.',
                ]);
            }
        });
    }

    protected $table = 'quality_inspection_items';

    protected $fillable = [
        'quality_inspection_id',
        'product_id',
        'lot_id',
        'inspected_qty',
        'passed_qty',
        'failed_qty',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'inspected_qty' => 'decimal:6',
            'passed_qty' => 'decimal:6',
            'failed_qty' => 'decimal:6',
        ];
    }

    public function qualityInspection(): BelongsTo
    {
        return $this->belongsTo(QualityInspection::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}
