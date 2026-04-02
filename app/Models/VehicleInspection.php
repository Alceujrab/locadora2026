<?php

namespace App\Models;

use App\Enums\InspectionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleInspection extends Model
{
    protected $attributes = [
        'fuel_level' => 100,
        'overall_condition' => 'bom',
        'status' => 'rascunho',
    ];

    protected $fillable = [
        'vehicle_id', 'contract_id', 'type', 'inspector_user_id',
        'mileage', 'fuel_level', 'inspection_date', 'overall_condition',
        'notes', 'status', 'pdf_path', 'signature_token', 'signed_at',
        'signature_ip', 'signature_hash', 'signature_image', 'signature_latitude',
        'signature_longitude', 'signature_method',
    ];

    protected $casts = [
        'type' => InspectionType::class,
        'inspection_date' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InspectionItem::class, 'inspection_id');
    }

    public function getDamagedItemsAttribute()
    {
        return $this->items->whereIn('condition', ['ruim', 'danificado']);
    }

    public function getTotalDamageValueAttribute(): float
    {
        return $this->items->sum('damage_value');
    }

    public function isSigned(): bool
    {
        return $this->signed_at !== null;
    }
}
