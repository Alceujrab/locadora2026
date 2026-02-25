<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'customer_id', 'vehicle_id', 'category_id',
        'pickup_date', 'return_date', 'pickup_branch_id', 'return_branch_id',
        'daily_rate', 'total_days', 'subtotal', 'extras_total', 'discount', 'total',
        'status', 'notes', 'canceled_at', 'cancel_reason', 'created_by',
    ];

    protected $casts = [
        'status' => ReservationStatus::class,
        'pickup_date' => 'datetime',
        'return_date' => 'datetime',
        'canceled_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function pickupBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'pickup_branch_id');
    }

    public function returnBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'return_branch_id');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(ReservationExtra::class);
    }

    public function contract(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Contract::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeByStatus($query, ReservationStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', [
            ReservationStatus::CANCELLED,
            ReservationStatus::COMPLETED,
        ]);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('pickup_date', '>=', now())
            ->whereIn('status', [ReservationStatus::PENDING, ReservationStatus::CONFIRMED]);
    }
}
