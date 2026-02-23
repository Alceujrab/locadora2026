<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'logo', 'cnpj', 'state_registration', 'phone', 'email',
        'address_street', 'address_number', 'address_complement',
        'address_neighborhood', 'address_city', 'address_state', 'address_zip',
        'is_active', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_street,
            $this->address_number,
            $this->address_complement,
            $this->address_neighborhood,
            $this->address_city,
            $this->address_state,
            $this->address_zip,
        ]);
        return implode(', ', $parts);
    }
}
