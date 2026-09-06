<?php

namespace App\Models;

use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'warehouse_id',
        'parent_id',
        'code',
        'name',
        'location_type',
        'level',
        'max_capacity',
        'capacity_uom',
        'is_pickable',
        'is_putaway_allowed',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'location_type' => LocationType::class,

            'max_capacity' => 'decimal:3',

            'is_pickable' => 'boolean',
            'is_putaway_allowed' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('location');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(
            Location::class,
            'parent_id'
        );
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(
            Uom::class,
            'capacity_uom'
        );
    }

    public function children(): HasMany
    {
        return $this->hasMany(
            Location::class,
            'parent_id'
        );
    }

    public function sourcePutawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class, 'source_bin_id');
    }

    public function destinationPutawayTasks(): HasMany
    {
        return $this->hasMany(PutawayTask::class, 'destination_bin_id');
    }
}
