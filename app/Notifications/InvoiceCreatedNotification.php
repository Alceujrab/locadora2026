<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \App\Models\Invoice $invoice
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $value = number_format($this->invoice->total_with_charges, 2, ',', '.');
        $dueDate = $this->invoice->due_date->format('d/m/Y');
        $name = explode(' ', $notifiable->name)[0];

        $mail = (new MailMessage)
            ->subject('Sua fatura da Locadora 2026 está pronta')
            ->greeting("Olá, {$name}!")
            ->line("Sua fatura vinculada ao Contrato #{$this->invoice->contract->contract_number} já está liberada.")
            ->line("Valor total: R$ {$value}")
            ->line("Vencimento: {$dueDate}");

        $paymentPix = $this->invoice->payments()->where('payment_method', 'pix')->first();
        if ($paymentPix && $paymentPix->pix_qr_code) {
            $mail->line('Copie o código PIX abaixo para pagamento imediato:')
                ->line($paymentPix->pix_qr_code);
        }

        return $mail->action('Acessar Portal do Cliente', route('cliente.login'))
            ->line('Obrigado por escolher a Locadora 2026!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'amount' => $this->invoice->total_with_charges,
        ];
    }
}
