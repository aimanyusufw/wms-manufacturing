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
            ->useLogName('warehouse');
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function rootLocations(): HasMany
    {
        return $this->hasMany(Location::class)
            ->whereNull('parent_id');
    }
}
