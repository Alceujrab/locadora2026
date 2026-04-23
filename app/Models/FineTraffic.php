<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FineTraffic extends Model
{
    use SoftDeletes;

    protected $table = 'fines_traffic';

    protected $fillable = [
        'vehicle_id', 'contract_id', 'customer_id', 'fine_code', 'description',
        'amount', 'fine_date', 'due_date', 'notification_date',
        'auto_infraction_number', 'status', 'responsibility', 'notes',
        // Condutor informado (indicação de condutor)
        'driver_name', 'driver_cpf', 'driver_rg', 'driver_phone', 'driver_email',
        'driver_cnh_number', 'driver_cnh_expires_at',
        'driver_zipcode', 'driver_address', 'driver_address_number', 'driver_address_complement',
        'driver_neighborhood', 'driver_city', 'driver_state',
        'driver_cnh_path', 'driver_address_proof_path',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fine_date' => 'date',
        'due_date' => 'date',
        'notification_date' => 'date',
        'driver_cnh_expires_at' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendente');
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status === 'pendente';
    }
}
