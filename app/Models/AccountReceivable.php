<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivable extends Model
{
    use SoftDeletes;

    protected $table = 'accounts_receivable';

    protected $fillable = [
        'branch_id', 'customer_id', 'contract_id', 'invoice_id', 'description',
        'amount', 'paid_amount', 'due_date', 'received_at', 'payment_method',
        'payer_name', 'payment_bank', 'payment_reference', 'payment_proof_path',
        'status', 'recurrence', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->amount - (float) $this->paid_amount);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pendente')->where('due_date', '<', now());
    }
}
