<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ServiceOrderStatus;

class ServiceOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'vehicle_id', 'supplier_id', 'type', 'description',
        'items_total', 'labor_total', 'total', 'status', 'opened_at',
        'completed_at', 'nf_number', 'nf_path', 'notes', 'created_by',
    ];

    protected $casts = [
        'status' => ServiceOrderStatus::class,
        'opened_at' => 'datetime',
        'completed_at' => 'datetime',
        'items_total' => 'decimal:2',
        'labor_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function items() { return $this->hasMany(ServiceOrderItem::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeOpen($query) { return $query->where('status', ServiceOrderStatus::OPEN); }
    public function scopeByVehicle($query, int $vehicleId) { return $query->where('vehicle_id', $vehicleId); }

    public function recalculateTotal(): void
    {
        $this->items_total = $this->items()->where('type', 'peca')->sum('total');
        $this->labor_total = $this->items()->where('type', 'mao_de_obra')->sum('total');
        $this->total = $this->items_total + $this->labor_total;
        $this->save();
    }
}
