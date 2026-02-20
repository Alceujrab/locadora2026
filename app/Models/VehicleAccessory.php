<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleAccessory extends Model
{
    protected $fillable = ['vehicle_id', 'name', 'description', 'is_included'];

    protected $casts = ['is_included' => 'boolean'];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
