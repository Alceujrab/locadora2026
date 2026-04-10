<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;

    protected string $instanceName;

    protected string $apiKey;

    public function __construct()
    {
        // Prioriza configurações do banco, fallback para .env
        $this->baseUrl = rtrim(
            Setting::get('evolution_api_url') ?? env('EVOLUTION_API_URL', ''),
            '/'
        );
        $this->instanceName = Setting::get('evolution_instance_name') ?? env('EVOLUTION_INSTANCE_NAME', '');
        $this->apiKey = Setting::get('evolution_api_key') ?? env('EVOLUTION_API_KEY', '');
    }

    /**
     * A Evolution Go autentica a instância pelo token enviado no header apikey.
     */
    protected function hasRequiredConfiguration(): bool
    {
        return ! empty($this->baseUrl) && ! empty($this->apiKey);
    }

    protected function baseHeaders(): array
    {
        return [
            'apikey' => $this->apiKey,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Verifica se o WhatsApp está habilitado no sistema
     */
    public function isEnabled(): bool
    {
        return (bool) Setting::get('whatsapp_enabled', false);
    }

    /**
     * Verifica se notificações de um módulo específico estão habilitadas
     */
    public function isModuleEnabled(string $module): bool
    {
        return $this->isEnabled() && (bool) Setting::get("whatsapp_notify_{$module}", true);
    }

    /**
     * Envia uma mensagem de texto via Evolution Go
     */
    public function sendText(string $number, string $text): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes da Evolution Go incompletas.', [
                'base_url_configured' => ! empty($this->baseUrl),
                'instance_token_configured' => ! empty($this->apiKey),
                'instance_name_configured' => ! empty($this->instanceName),
            ]);
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/send/text";

            $response = Http::withHeaders($this->baseHeaders())
                ->asJson()
                ->timeout(30)
                ->post($endpoint, [
                    'number' => $this->formatNumber($number),
                    'text' => $text,
                    'formatJid' => true,
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp disparado p/ {$number}");
                return $response->json();
            }

            Log::error("Erro Evolution Go no envio p/ {$number}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção disparando WhatsApp: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Formata o número para o padrão DDI + DDD da Evolution
     */
    protected function formatNumber(string $number): string
    {
        $number = preg_replace('/[^0-9]/', '', $number);

        if (str_starts_with($number, '0')) {
            $number = substr($number, 1);
        }

        if (strlen($number) <= 11) {
            $number = '55'.$number;
        }

        return $number;
    }

    /**
     * Envia um documento (PDF) via Evolution Go.
     */
    public function sendDocument(string $number, string $mediaUrl, string $fileName, ?string $caption = null): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes da Evolution Go incompletas.', [
                'base_url_configured' => ! empty($this->baseUrl),
                'instance_token_configured' => ! empty($this->apiKey),
                'instance_name_configured' => ! empty($this->instanceName),
            ]);
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/send/media";

            $payload = [
                'number' => $this->formatNumber($number),
                'type' => 'document',
                'url' => $mediaUrl,
                'mediaUrl' => $mediaUrl,
                'filename' => $fileName,
                'fileName' => $fileName,
                'formatJid' => true,
            ];

            if ($caption) {
                $payload['caption'] = $caption;
            }

            $response = Http::withHeaders($this->baseHeaders())
                ->asJson()
                ->timeout(60)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("Documento WhatsApp enviado p/ {$number}: {$fileName}");
                return $response->json();
            }

            Log::error("Erro Evolution Go no envio de documento p/ {$number}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar documento WhatsApp: '.$e->getMessage());
            return null;
        }
    }

    /**
     * Consulta o status da instância autenticada pelo token configurado.
     */
    public function getConnectionStatus(): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes da Evolution Go incompletas para consulta de status.');
            return null;
        }

        try {
            $response = Http::withHeaders($this->baseHeaders())
                ->timeout(15)
                ->get("{$this->baseUrl}/instance/status");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro Evolution Go ao consultar status da instancia.', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Excecao ao consultar status da Evolution Go: '.$e->getMessage());
            return null;
        }
    }
}
