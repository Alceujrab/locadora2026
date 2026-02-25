<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'invoice_id', 'amount', 'method', 'mp_payment_id', 'mp_status',
        'transaction_id', 'pix_qr_code', 'pix_qr_code_base64',
        'boleto_url', 'boleto_barcode', 'paid_at', 'refunded_at',
        'refund_amount', 'notes',
    ];

    protected $casts = [
        'method' => PaymentMethod::class,
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function isRefunded(): bool
    {
        return $this->refunded_at !== null;
    }

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }
}
