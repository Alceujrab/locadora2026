<?php

namespace App\Models;

use App\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasUuids, SoftDeletes;

    protected $attributes = [
        'pickup_mileage' => 0,
        'return_mileage' => null,
        'extras_total' => 0,
        'caution_amount' => 0,
        'discount' => 0,
        'additional_charges' => 0,
    ];

    protected $fillable = [
        'branch_id', 'reservation_id', 'customer_id', 'vehicle_id', 'template_id',
        'contract_number', 'pickup_date', 'return_date', 'actual_return_date',
        'pickup_mileage', 'return_mileage', 'daily_rate', 'total_days',
        'extras_total', 'caution_amount', 'discount', 'additional_charges',
        'additional_charges_description', 'total', 'status',
        'signed_at', 'signature_token', 'signature_ip', 'signature_hash',
        'signature_method', 'signature_image', 'signature_latitude', 'signature_longitude',
        'pdf_path', 'notes', 'created_by',
    ];

    protected $casts = [
        'status' => ContractStatus::class,
        'pickup_date' => 'datetime',
        'return_date' => 'datetime',
        'actual_return_date' => 'datetime',
        'signed_at' => 'datetime',
        'daily_rate' => 'decimal:2',
        'extras_total' => 'decimal:2',
        'caution_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function extras(): HasMany
    {
        return $this->hasMany(ContractExtra::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ContractLog::class)->orderByDesc('created_at');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }

    public function caution(): HasOne
    {
        return $this->hasOne(Caution::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', ContractStatus::ACTIVE);
    }

    public function scopeByStatus($query, ContractStatus $status)
    {
        return $query->where('status', $status);
    }

    // Helpers
    public function isSigned(): bool
    {
        return $this->signed_at !== null;
    }

    public function isActive(): bool
    {
        return $this->status === ContractStatus::ACTIVE;
    }

    public function isOverdue(): bool
    {
        return $this->isActive() && $this->return_date->isPast() && $this->actual_return_date === null;
    }

    public function getKmDrivenAttribute(): ?int
    {
        return $this->return_mileage ? $this->return_mileage - $this->pickup_mileage : null;
    }

    public static function generateContractNumber(): string
    {
        $prefix = 'LOC';
        $year = now()->format('Y');
        $lastNumber = static::withTrashed()
            ->where('contract_number', 'like', sprintf('%s-%s-%%', $prefix, $year))
            ->orderByDesc('contract_number')
            ->value('contract_number');

        $nextSequence = 1;

        if (is_string($lastNumber) && preg_match('/^'.$prefix.'-'.$year.'-(\d{5})$/', $lastNumber, $matches)) {
            $nextSequence = ((int) $matches[1]) + 1;
        }

        do {
            $contractNumber = sprintf('%s-%s-%05d', $prefix, $year, $nextSequence);
            $exists = static::withTrashed()
                ->where('contract_number', $contractNumber)
                ->exists();
            $nextSequence++;
        } while ($exists);

        return $contractNumber;
    }
}
