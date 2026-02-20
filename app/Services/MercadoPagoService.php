<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Exception;

class MercadoPagoService
{
    protected string $accessToken;
    protected string $baseUrl = 'https://api.mercadopago.com/v1';

    public function __construct()
    {
        $this->accessToken = config('services.mercadopago.access_token', env('MERCADOPAGO_ACCESS_TOKEN', 'APP_USR-TEST-TOKEN'));
    }

    /**
     * Gera um pagamento PIX via Mercado Pago
     */
    public function generatePix(Payment $payment, Invoice $invoice): array|null
    {
        try {
            $customer = $invoice->customer;
            $customerName = explode(' ', $customer->name);

            $payload = [
                'transaction_amount' => (float) $payment->amount,
                'description' => "Fatura #" . $invoice->invoice_number,
                'payment_method_id' => 'pix',
                'payer' => [
                    'email' => $customer->email ?? 'cliente@locadora.com.br',
                    'first_name' => $customerName[0] ?? 'Cliente',
                    'last_name' => isset($customerName[1]) ? end($customerName) : 'Locadora',
                    'identification' => [
                        'type' => strlen(preg_replace('/\D/', '', $customer->document_number)) == 14 ? 'CNPJ' : 'CPF',
                        'number' => preg_replace('/\D/', '', $customer->document_number),
                    ]
                ],
            ];

            $response = Http::withToken($this->accessToken)
                ->withHeaders(['X-Idempotency-Key' => $payment->id])
                ->post("{$this->baseUrl}/payments", $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                $payment->update([
                    'mp_payment_id' => (string) $data['id'],
                    'mp_status' => $data['status'],
                    'pix_qr_code' => $data['point_of_interaction']['transaction_data']['qr_code'] ?? null,
                    'pix_qr_code_base64' => $data['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null,
                ]);

                return $data;
            }

            Log::error('MercadoPago PIX Error: ' . $response->body());
            return null;
        } catch (Exception $e) {
            Log::error('MercadoPago Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Processa atualizações de status via Webhook (IPN) do Mercado Pago
     */
    public function handleWebhook(array $payload)
    {
        try {
            if (!isset($payload['data']['id'])) {
                return false;
            }

            $mpPaymentId = $payload['data']['id'];

            // Consultar M.P. para status real (Boa Prática de Segurança)
            $response = Http::withToken($this->accessToken)
                ->get("{$this->baseUrl}/payments/{$mpPaymentId}");

            if (!$response->successful()) {
                Log::error('MercadoPago Webhook falhou ao conferir ID: ' . $mpPaymentId);
                return false;
            }

            $data = $response->json();
            $status = $data['status']; // 'approved', 'pending', 'rejected', 'cancelled'

            // Encontrar o Payment local
            $payment = Payment::where('mp_payment_id', (string) $mpPaymentId)->first();

            if (!$payment) {
                Log::warning("Webhook recebido para Pagamento não encontrado localmente: MP_ID {$mpPaymentId}");
                return false;
            }

            $payment->update(['mp_status' => $status]);

            if ($status === 'approved' && !$payment->isPaid()) {
                $payment->update([
                    'paid_at' => now(),
                ]);

                // Atualiza Fatura correspondente se todo valor for abatido
                $invoice = $payment->invoice;
                if ($invoice) {
                    $totalPaid = $invoice->payments()->whereNotNull('paid_at')->sum('amount');
                    if ($totalPaid >= $invoice->total) {
                        $invoice->update([
                            'status' => InvoiceStatus::PAID,
                            'paid_at' => now(),
                        ]);
                    }
                }
            }

            return true;
        } catch (Exception $e) {
            Log::error('Erro ao processar Webhook MP: ' . $e->getMessage());
            return false;
        }
    }
}
