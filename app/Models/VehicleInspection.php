<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\InspectionType;

class VehicleInspection extends Model
{
    protected $fillable = [
        'vehicle_id', 'contract_id', 'type', 'inspector_user_id',
        'mileage', 'fuel_level', 'inspection_date', 'overall_condition',
        'notes', 'status',
    ];

    protected $casts = [
        'type' => InspectionType::class,
        'inspection_date' => 'datetime',
    ];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function inspector(): BelongsTo { return $this->belongsTo(User::class, 'inspector_user_id'); }
    public function items(): HasMany { return $this->hasMany(InspectionItem::class, 'inspection_id'); }

    public function getDamagedItemsAttribute()
    {
        return $this->items->whereIn('condition', ['ruim', 'danificado']);
    }

    public function getTotalDamageValueAttribute(): float
    {
        return $this->items->sum('damage_value');
    }
}
