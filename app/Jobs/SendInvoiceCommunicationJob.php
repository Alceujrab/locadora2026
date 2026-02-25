<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Notifications\InvoiceCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendInvoiceCommunicationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $customer = $this->invoice->customer;

        if (! $customer) {
            Log::warning("SendInvoiceCommunicationJob: Fatura {$this->invoice->id} sem cliente vinculado.");

            return;
        }

        // 1. Enviar Notification (Email)
        if (! empty($customer->email)) {
            $customer->notify(new InvoiceCreatedNotification($this->invoice));
            Log::info("Email de fatura agendado p/ {$customer->email}");
        }

        // 2. Enviar WhatsApp via Evolution API
        $phone = $customer->phone ?? $customer->whatsapp;
        if (! empty($phone)) {
            $name = explode(' ', $customer->name)[0];
            $value = number_format($this->invoice->total_with_charges, 2, ',', '.');
            $dueDate = $this->invoice->due_date->format('d/m/Y');

            $message = "Olá *{$name}*!\n\n";
            $message .= "Sua fatura da *Locadora 2026* já está disponível.\n";
            $message .= "Valor: *R$ {$value}*\n";
            $message .= "Vencimento: *{$dueDate}*\n\n";

            // Busca o PIX se houver integração no pagamento recém gerado
            $paymentPix = $this->invoice->payments()->where('payment_method', 'pix')->first();
            if ($paymentPix && $paymentPix->pix_qr_code) {
                $message .= "Pague rapidamente via PIX Copia e Cola abaixo:\n\n";
                $message .= "{$paymentPix->pix_qr_code}\n\n";
            }

            $message .= "Acesse o portal do cliente para mais detalhes:\n";
            $message .= route('cliente.login');

            SendWhatsAppMessageJob::dispatch($phone, $message);
        }
    }
}
