<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\VehicleStatus;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'category_id', 'plate', 'renavam', 'chassis',
        'brand', 'model', 'year_manufacture', 'year_model', 'color',
        'fuel', 'transmission', 'doors', 'seats', 'trunk_capacity', 'mileage',
        'status', 'daily_rate_override', 'weekly_rate_override', 'monthly_rate_override',
        'insurance_value', 'fipe_value', 'purchase_value', 'purchase_date',
        'ipva_due_date', 'licensing_due_date', 'insurance_expiry_date',
        'is_featured', 'description', 'notes',
    ];

    protected $casts = [
        'status' => VehicleStatus::class,
        'is_featured' => 'boolean',
        'purchase_date' => 'date',
        'ipva_due_date' => 'date',
        'licensing_due_date' => 'date',
        'insurance_expiry_date' => 'date',
        'daily_rate_override' => 'decimal:2',
        'weekly_rate_override' => 'decimal:2',
        'monthly_rate_override' => 'decimal:2',
        'insurance_value' => 'decimal:2',
        'fipe_value' => 'decimal:2',
        'purchase_value' => 'decimal:2',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(VehiclePhoto::class)->orderBy('position');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(VehicleDocument::class);
    }

    public function accessories(): HasMany
    {
        return $this->hasMany(VehicleAccessory::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }

    public function serviceOrders(): HasMany
    {
        return $this->hasMany(ServiceOrder::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(FineTraffic::class);
    }

    public function maintenanceAlerts(): HasMany
    {
        return $this->hasMany(MaintenanceAlert::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', VehicleStatus::AVAILABLE);
    }

    public function scopeByBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getDailyRateAttribute(): float
    {
        return $this->daily_rate_override ?? $this->category?->daily_rate ?? 0;
    }

    public function getWeeklyRateAttribute(): float
    {
        return $this->weekly_rate_override ?? $this->category?->weekly_rate ?? 0;
    }

    public function getMonthlyRateAttribute(): float
    {
        return $this->monthly_rate_override ?? $this->category?->monthly_rate ?? 0;
    }

    public function getCoverPhotoAttribute(): ?string
    {
        $cover = $this->photos->firstWhere('is_cover', true);
        return $cover?->path ?? $this->photos->first()?->path;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->brand} {$this->model} {$this->year_model}";
    }

    // Methods
    public function isAvailable(): bool
    {
        return $this->status === VehicleStatus::AVAILABLE;
    }

    public function isAvailableForPeriod(\DateTime $start, \DateTime $end, ?int $ignoreReservationId = null): bool
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $query = $this->reservations()
            ->whereNotIn('status', [
                \App\Enums\ReservationStatus::CANCELLED,
                \App\Enums\ReservationStatus::COMPLETED,
            ])
            ->where(function ($q) use ($start, $end) {
                $q->where('pickup_date', '<=', $end)
                  ->where('return_date', '>=', $start);
            });

        if ($ignoreReservationId) {
            $query->where('id', '!=', $ignoreReservationId);
        }

        return !$query->exists();
    }
}
