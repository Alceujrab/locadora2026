<?php

namespace App\Models;

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id', 'vehicle_id', 'supplier_id', 'type', 'description',
        'requested_by', 'vehicle_city', 'procedure_adopted', 'driver_phone',
        'opened_by', 'customer_id',
        'items_total', 'labor_total', 'total', 'status', 'opened_at',
        'completed_at', 'nf_number', 'nf_path', 'attachments', 'pdf_path',
        'signature_token', 'signed_at', 'signature_ip', 'signature_hash', 'signature_image',
        'closed_at', 'closing_notes',
        'notes', 'created_by',
    ];

    protected $casts = [
        'status' => ServiceOrderStatus::class,
        'opened_at' => 'datetime',
        'completed_at' => 'datetime',
        'signed_at' => 'datetime',
        'closed_at' => 'datetime',
        'items_total' => 'decimal:2',
        'labor_total' => 'decimal:2',
        'total' => 'decimal:2',
        'attachments' => 'array',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceOrderItem::class);
    }

    public function orderNotes()
    {
        return $this->hasMany(ServiceOrderNote::class)->latest();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', ServiceOrderStatus::OPEN);
    }

    public function scopeByVehicle($query, int $vehicleId)
    {
        return $query->where('vehicle_id', $vehicleId);
    }

    // Methods
    public function isSigned(): bool
    {
        return ! is_null($this->signed_at);
    }

    public function recalculateTotal(): void
    {
        $this->items_total = $this->items()->where('type', 'peca')->sum('total');
        $this->labor_total = $this->items()->where('type', 'mao_de_obra')->sum('total');
        $this->total = $this->items_total + $this->labor_total;
        $this->save();
    }
}
