<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractExtra extends Model
{
    protected $fillable = ['contract_id', 'rental_extra_id', 'quantity', 'unit_price', 'total'];

    protected $casts = ['unit_price' => 'decimal:2', 'total' => 'decimal:2'];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function rentalExtra()
    {
        return $this->belongsTo(RentalExtra::class);
    }
}
