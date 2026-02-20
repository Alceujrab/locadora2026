<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'action', 'model_type', 'model_id',
        'old_values', 'new_values', 'ip', 'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Short model name for display
     */
    public function getModelNameAttribute(): string
    {
        $class = class_basename($this->model_type);
        return match($class) {
            'Contract' => 'Contrato',
            'Invoice' => 'Fatura',
            'Vehicle' => 'Veículo',
            'FineTraffic' => 'Multa',
            'Customer' => 'Cliente',
            'ServiceOrder' => 'Ordem de Serviço',
            'Nfse' => 'NFS-e',
            default => $class,
        };
    }
}
