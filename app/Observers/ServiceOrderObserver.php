<?php

namespace App\Observers;

use App\Models\ServiceOrder;

class ServiceOrderObserver
{
    /**
     * Handle the ServiceOrder "created" event.
     */
    public function created(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "updated" event.
     */
    public function updated(ServiceOrder $serviceOrder): void
    {
        // Se a OS foi marcada como Concluída agora, e possui Fornecedor e Valor
        if ($serviceOrder->wasChanged('status') && 
            $serviceOrder->status === \App\Enums\ServiceOrderStatus::COMPLETED && 
            $serviceOrder->total > 0 && 
            $serviceOrder->supplier_id) 
        {
            // Cria a dívida no Contas a Pagar automaticamente
            \App\Models\AccountPayable::create([
                'branch_id' => $serviceOrder->branch_id,
                'supplier_id' => $serviceOrder->supplier_id,
                'description' => "Manutenção OS #{$serviceOrder->id} - Viatura: " . ($serviceOrder->vehicle->title ?? 'N/D'),
                'amount' => $serviceOrder->total,
                'due_date' => now()->addDays(15), // Padrão de 15 dias, pode ser ajustado
                'status' => \App\Enums\AccountPayableStatus::OPEN,
                'reference_type' => 'ServiceOrder',
                'reference_id' => $serviceOrder->id,
            ]);
        }
    }

    /**
     * Handle the ServiceOrder "deleted" event.
     */
    public function deleted(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "restored" event.
     */
    public function restored(ServiceOrder $serviceOrder): void
    {
        //
    }

    /**
     * Handle the ServiceOrder "force deleted" event.
     */
    public function forceDeleted(ServiceOrder $serviceOrder): void
    {
        //
    }
}
