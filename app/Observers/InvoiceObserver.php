<?php

namespace App\Observers;

use App\Models\Invoice;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        if ($invoice->status === \App\Enums\InvoiceStatus::OPEN) {
            try {
                \App\Jobs\SendInvoiceCommunicationJob::dispatch($invoice);
            } catch (\Throwable $e) {
                \Log::warning("Falha ao enviar notificação de fatura {$invoice->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status') && $invoice->status === \App\Enums\InvoiceStatus::OPEN) {
            try {
                \App\Jobs\SendInvoiceCommunicationJob::dispatch($invoice);
            } catch (\Throwable $e) {
                \Log::warning("Falha ao enviar notificação de fatura {$invoice->id}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     */
    public function deleted(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
