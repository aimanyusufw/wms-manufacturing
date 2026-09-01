<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Warehouse extends Model
{
    use SoftDeletes, HasFactory, LogsActivity;

    protected $table = 'warehouses';

    protected $fillable = [
        'code',
        'name',
        'address',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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

    public function zones(): HasMany
    {
        return $this->hasMany(Zone::class, "warehouse_id");
    }

    public function bins(): HasMany
    {
        return $this->hasMany(Bin::class, "warehouse_id");
    }
}
