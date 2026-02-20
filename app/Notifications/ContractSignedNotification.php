<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContractSignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public \App\Models\Contract $contract
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
        $name = explode(' ', $notifiable->name)[0];
        $vehicle = $this->contract->vehicle->title ?? 'Veículo';

        $mail = (new MailMessage)
                    ->subject("Cópia do seu Contrato - Locadora 2026")
                    ->greeting("Olá, {$name}!")
                    ->line("Sua locação do veículo {$vehicle} foi confirmada e o contrato assinado digitalmente.")
                    ->line("O arquivo com as vias completas do seu contrato está anexado ou disponível no seu Portal.");

        if ($this->contract->pdf_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->contract->pdf_path)) {
            $mail->attach(\Illuminate\Support\Facades\Storage::disk('public')->path($this->contract->pdf_path));
        }

        return $mail->action('Acessar Painel do Cliente', route('cliente.login'))
                    ->line('Desejamos uma ótima viagem!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contract_id' => $this->contract->id,
            'status' => 'signed'
        ];
    }
}
