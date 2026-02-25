<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caution extends Model
{
    protected $fillable = [
        'contract_id', 'customer_id', 'type', 'amount', 'mp_payment_id',
        'mp_preauth_id', 'status', 'released_at', 'charged_amount',
        'charge_reason', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charged_amount' => 'decimal:2',
        'released_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isRetained(): bool
    {
        return $this->status === 'retida';
    }

    public function isReleased(): bool
    {
        return $this->status === 'liberada';
    }
}
