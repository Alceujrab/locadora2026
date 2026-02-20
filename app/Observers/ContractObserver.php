<?php

namespace App\Observers;

use App\Models\Contract;

class ContractObserver
{
    /**
     * Handle the Contract "created" event.
     */
    public function created(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "updated" event.
     */
    public function updated(\App\Models\Contract $contract): void
    {
        if ($contract->wasChanged('status') && $contract->status === \App\Enums\ContractStatus::ACTIVE) {
            $customer = $contract->customer;
            
            if ($customer) {
                // Email
                if (!empty($customer->email)) {
                    $customer->notify(new \App\Notifications\ContractSignedNotification($contract));
                }

                // WhatsApp
                $phone = $customer->phone ?? $customer->whatsapp;
                if (!empty($phone)) {
                    $name = explode(' ', $customer->name)[0];
                    $message = "Olá *{$name}*!\n\n";
                    $message .= "Seu contrato com a *Locadora 2026* foi assinado digitalmente.\n";
                    $message .= "Contrato: *#{$contract->contract_number}*\n\n";
                    $message .= "Você pode baixar a sua via assinada no portal do cliente:\n" . route('cliente.login');
                    
                    \App\Jobs\SendWhatsAppMessageJob::dispatch($phone, $message);
                }
            }
        }
    }

    /**
     * Handle the Contract "deleted" event.
     */
    public function deleted(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "restored" event.
     */
    public function restored(Contract $contract): void
    {
        //
    }

    /**
     * Handle the Contract "force deleted" event.
     */
    public function forceDeleted(Contract $contract): void
    {
        //
    }
}
