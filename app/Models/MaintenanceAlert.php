<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceAlert extends Model
{
    protected $fillable = [
        'vehicle_id', 'type', 'description', 'trigger_km', 'trigger_days',
        'last_service_date', 'last_service_km', 'is_active', 'last_triggered_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_service_date' => 'date',
        'last_triggered_at' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
