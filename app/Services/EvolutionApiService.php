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
     * Envia uma mensagem de texto via Evolution API
     */
    public function sendText(string $number, string $text): ?array
    {
        if (empty($this->baseUrl) || empty($this->instanceName) || empty($this->apiKey)) {
            Log::warning('Configurações da Evolution API incompletas.');
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/message/sendText/{$this->instanceName}";

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(30)
                ->post($endpoint, [
                    'number' => $this->formatNumber($number),
                    'text' => $text,
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp disparado p/ {$number}");
                return $response->json();
            }

            Log::error("Erro Evolution API no envio p/ {$number}", [
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
     * Envia um documento (PDF) via Evolution API
     */
    public function sendDocument(string $number, string $mediaUrl, string $fileName, ?string $caption = null): ?array
    {
        if (empty($this->baseUrl) || empty($this->instanceName) || empty($this->apiKey)) {
            Log::warning('Configurações da Evolution API incompletas.');
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/message/sendMedia/{$this->instanceName}";

            $payload = [
                'number' => $this->formatNumber($number),
                'mediatype' => 'document',
                'media' => $mediaUrl,
                'fileName' => $fileName,
            ];

            if ($caption) {
                $payload['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(60)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                Log::info("Documento WhatsApp enviado p/ {$number}: {$fileName}");
                return $response->json();
            }

            Log::error("Erro Evolution API no envio de documento p/ {$number}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar documento WhatsApp: '.$e->getMessage());
            return null;
        }
    }
}
