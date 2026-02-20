<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountReceivable extends Model
{
    use SoftDeletes;

    protected $table = 'accounts_receivable';

    protected $fillable = [
        'branch_id', 'customer_id', 'contract_id', 'invoice_id', 'category', 'description',
        'amount', 'due_date', 'received_at', 'payment_method', 'status',
        'recurrence', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'received_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function contract() { return $this->belongsTo(Contract::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }

    public function scopePending($query) { return $query->where('status', 'pendente'); }
    public function scopeOverdue($query) { return $query->where('status', 'pendente')->where('due_date', '<', now()); }
}
