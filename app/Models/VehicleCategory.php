<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'name', 'description', 'daily_rate', 'weekly_rate',
        'monthly_rate', 'km_type', 'km_rate', 'km_included',
        'insurance_daily', 'icon', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'weekly_rate' => 'decimal:2',
        'monthly_rate' => 'decimal:2',
        'km_rate' => 'decimal:2',
        'insurance_daily' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getAvailableVehiclesCountAttribute(): int
    {
        return $this->vehicles()->available()->count();
    }
}
