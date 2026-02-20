<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountPayable extends Model
{
    use SoftDeletes;

    protected $table = 'accounts_payable';

    protected $fillable = [
        'branch_id', 'supplier_id', 'vehicle_id', 'category', 'description',
        'amount', 'due_date', 'paid_at', 'payment_method', 'status',
        'recurrence', 'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }

    public function scopePending($query) { return $query->where('status', 'pendente'); }
    public function scopeOverdue($query) { return $query->where('status', 'pendente')->where('due_date', '<', now()); }
}
