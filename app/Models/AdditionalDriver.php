<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdditionalDriver extends Model
{
    protected $fillable = [
        'customer_id', 'name', 'cpf', 'cnh_number', 'cnh_category', 'cnh_expiry', 'phone',
    ];

    protected $casts = ['cnh_expiry' => 'date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
