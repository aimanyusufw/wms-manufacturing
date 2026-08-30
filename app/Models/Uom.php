<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Uom extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'uoms';

    protected $fillable = [
        'code',
        'name',
        'decimal_places',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('uom');
    }

    /**
     * Relasi ke Product (sebagai base UOM)
     */
    public function baseProductsUom(): HasMany
    {
        return $this->hasMany(Product::class, 'base_uom_id');
    }

    /**
     * Relasi ke ProductUom
     */
    public function productUoms(): HasMany
    {
        return $this->hasMany(ProductUom::class, 'uom_id');
    }
}
