<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'base_uom_id',
        'sku',
        'barcode',
        'name',
        'product_type',
        'description',
        'min_stock',
        'max_stock',
        'reorder_point',
        'shelf_life_days',
        'track_lot',
        'track_serial',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_stock' => 'decimal:6',
            'max_stock' => 'decimal:6',
            'reorder_point' => 'decimal:6',
            'track_lot' => 'boolean',
            'track_serial' => 'boolean',
            'is_active' => 'boolean',
            'product_type' => ProductType::class
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('product');
    }

    /**
     * Relasi ke ProductCategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Relasi ke Unit of Measure (base UOM)
     */
    public function baseUom(): BelongsTo
    {
        return $this->belongsTo(Uom::class, 'base_uom_id');
    }

    /**
     * Relasi ke ProductUom (Unit of Measure untuk produk)
     */
    public function uoms(): HasMany
    {
        return $this->hasMany(ProductUom::class, 'product_id');
    }

    /**
     * Relasi many-to-many ke Uom melalui ProductUom
     */
    public function allUoms(): BelongsToMany
    {
        return $this->belongsToMany(
            Uom::class,
            'product_uoms',
            'product_id',
            'uom_id'
        )
            ->withPivot(['conversion_factor', 'is_purchase_uom', 'is_sales_uom'])
            ->withTimestamps();
    }

    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function qualityInspectionItems(): HasMany
    {
        return $this->hasMany(QualityInspectionItem::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }
}
