<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\CustomerType;

class Customer extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id', 'user_id', 'type', 'name', 'cpf_cnpj', 'rg', 'birth_date',
        'email', 'phone', 'whatsapp', 'cnh_number', 'cnh_category', 'cnh_expiry',
        'company_name', 'state_registration', 'responsible_name', 'responsible_cpf',
        'address_street', 'address_number', 'address_complement',
        'address_neighborhood', 'address_city', 'address_state', 'address_zip',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'is_blocked', 'blocked_reason', 'notes',
    ];

    protected $casts = [
        'type' => CustomerType::class,
        'birth_date' => 'date',
        'cnh_expiry' => 'date',
        'is_blocked' => 'boolean',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function additionalDrivers(): HasMany
    {
        return $this->hasMany(AdditionalDriver::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(FineTraffic::class);
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_blocked', false);
    }

    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    public function scopePf($query)
    {
        return $query->where('type', 'pf');
    }

    public function scopePj($query)
    {
        return $query->where('type', 'pj');
    }

    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('cpf_cnpj', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedCpfCnpjAttribute(): string
    {
        $doc = preg_replace('/\D/', '', $this->cpf_cnpj);
        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
    }

    public function isCnhExpired(): bool
    {
        return $this->cnh_expiry && $this->cnh_expiry->isPast();
    }

    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()->where('status', 'vencida')->exists();
    }
}
