<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationExtra extends Model
{
    protected $fillable = ['reservation_id', 'rental_extra_id', 'quantity', 'unit_price', 'total'];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function rentalExtra(): BelongsTo
    {
        return $this->belongsTo(RentalExtra::class);
    }
}
