<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\InvoiceStatus;

class Invoice extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id', 'contract_id', 'customer_id', 'invoice_number', 'due_date',
        'installment_number', 'total_installments', 'amount', 'penalty_amount',
        'interest_amount', 'discount', 'total', 'status', 'paid_at', 'payment_method',
        'mp_payment_id', 'nfse_number', 'nfse_xml_path', 'nfse_pdf_path', 'notes',
    ];

    protected $casts = [
        'status' => InvoiceStatus::class,
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'penalty_amount' => 'decimal:2',
        'interest_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }

    // Scopes
    public function scopeOverdue($query) { return $query->where('status', InvoiceStatus::OVERDUE); }
    public function scopeOpen($query) { return $query->where('status', InvoiceStatus::OPEN); }
    public function scopePaid($query) { return $query->where('status', InvoiceStatus::PAID); }
    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->where('status', InvoiceStatus::OPEN)
                     ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Helpers
    public function isOverdue(): bool
    {
        return in_array($this->status, [InvoiceStatus::OPEN, InvoiceStatus::OVERDUE]) && $this->due_date->isPast();
    }

    public function calculatePenaltyAndInterest(): array
    {
        if (!$this->isOverdue()) return ['penalty' => 0, 'interest' => 0];

        $daysOverdue = $this->due_date->diffInDays(now());
        $penalty = $this->amount * 0.02; // 2%
        $monthlyRate = 0.01; // 1% a.m.
        $interest = $this->amount * $monthlyRate * ($daysOverdue / 30);

        return [
            'penalty' => round($penalty, 2),
            'interest' => round($interest, 2),
        ];
    }

    public function getTotalWithChargesAttribute(): float
    {
        return $this->amount + $this->penalty_amount + $this->interest_amount - $this->discount;
    }
}
