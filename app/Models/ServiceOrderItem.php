<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOrderItem extends Model
{
    protected $fillable = ['service_order_id', 'type', 'description', 'quantity', 'unit_price', 'total'];
    protected $casts = ['unit_price' => 'decimal:2', 'total' => 'decimal:2'];

    public function serviceOrder() { return $this->belongsTo(ServiceOrder::class); }
}
