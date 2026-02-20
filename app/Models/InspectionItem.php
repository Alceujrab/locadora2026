<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InspectionItem extends Model
{
    protected $fillable = [
        'inspection_id', 'category', 'item_name', 'condition',
        'damage_description', 'damage_value', 'photos',
    ];

    protected $casts = [
        'damage_value' => 'decimal:2',
        'photos' => 'array',
    ];

    public function inspection(): BelongsTo { return $this->belongsTo(VehicleInspection::class, 'inspection_id'); }
    public function isDamaged(): bool { return in_array($this->condition, ['ruim', 'danificado']); }
}
