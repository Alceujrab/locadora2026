<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente para a API WuzAPI (https://github.com/asternic/wuzapi).
 *
 * - Autenticação: header "token" com o token do usuário criado no wuzapi.
 * - Endpoints usados:
 *   POST /chat/send/text       Envio de texto
 *   POST /chat/send/document   Envio de documento (PDF) em base64
 *   GET  /session/status       Status da sessão conectada
 *
 * Para manter compatibilidade durante a transição, o construtor lê primeiro
 * as chaves novas (wuzapi_*) e faz fallback para as antigas (evolution_*)
 * previamente salvas no banco.
 */
class WuzapiService
{
    protected string $baseUrl;

    protected string $token;

    protected string $instanceLabel;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            Setting::get('wuzapi_api_url')
                ?? Setting::get('evolution_api_url')
                ?? env('WUZAPI_API_URL', env('EVOLUTION_API_URL', '')),
            '/'
        );

        $this->token = Setting::get('wuzapi_token')
            ?? Setting::get('evolution_api_key')
            ?? env('WUZAPI_TOKEN', env('EVOLUTION_API_KEY', ''));

        $this->instanceLabel = Setting::get('wuzapi_instance_label')
            ?? Setting::get('evolution_instance_name')
            ?? env('WUZAPI_INSTANCE_LABEL', '');
    }

    protected function hasRequiredConfiguration(): bool
    {
        return ! empty($this->baseUrl) && ! empty($this->token);
    }

    protected function baseHeaders(): array
    {
        return [
            'token' => $this->token,
            'Accept' => 'application/json',
        ];
    }

    public function isEnabled(): bool
    {
        return (bool) Setting::get('whatsapp_enabled', false);
    }

    public function isModuleEnabled(string $module): bool
    {
        return $this->isEnabled() && (bool) Setting::get("whatsapp_notify_{$module}", true);
    }

    public function sendText(string $number, string $text): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes do WuzAPI incompletas.', [
                'base_url_configured' => ! empty($this->baseUrl),
                'token_configured' => ! empty($this->token),
            ]);
            return null;
        }

        try {
            $response = Http::withHeaders($this->baseHeaders())
                ->asJson()
                ->timeout(30)
                ->post("{$this->baseUrl}/chat/send/text", [
                    'Phone' => $this->formatNumber($number),
                    'Body' => $text,
                ]);

            if ($response->successful()) {
                Log::info("WhatsApp (WuzAPI) disparado p/ {$number}");
                return $response->json();
            }

            Log::error("Erro WuzAPI no envio p/ {$number}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Excecao disparando WhatsApp (WuzAPI): '.$e->getMessage());
            return null;
        }
    }

    public function sendDocument(string $number, string $mediaUrl, string $fileName, ?string $caption = null): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes do WuzAPI incompletas (documento).');
            return null;
        }

        try {
            $base64 = $this->fetchAsBase64($mediaUrl);
            if ($base64 === null) {
                Log::error("Falha ao baixar documento para envio WuzAPI: {$mediaUrl}");
                return null;
            }

            $payload = [
                'Phone' => $this->formatNumber($number),
                'Document' => 'data:application/pdf;base64,'.$base64,
                'FileName' => $fileName,
            ];

            if ($caption) {
                $payload['Caption'] = $caption;
            }

            $response = Http::withHeaders($this->baseHeaders())
                ->asJson()
                ->timeout(60)
                ->post("{$this->baseUrl}/chat/send/document", $payload);

            if ($response->successful()) {
                Log::info("Documento WhatsApp (WuzAPI) enviado p/ {$number}: {$fileName}");
                return $response->json();
            }

            Log::error("Erro WuzAPI no envio de documento p/ {$number}", [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Excecao ao enviar documento WhatsApp (WuzAPI): '.$e->getMessage());
            return null;
        }
    }

    public function getConnectionStatus(): ?array
    {
        if (! $this->hasRequiredConfiguration()) {
            Log::warning('Configuracoes do WuzAPI incompletas para consulta de status.');
            return null;
        }

        try {
            $response = Http::withHeaders($this->baseHeaders())
                ->timeout(15)
                ->get("{$this->baseUrl}/session/status");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Erro WuzAPI ao consultar status da sessao.', [
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Excecao ao consultar status do WuzAPI: '.$e->getMessage());
            return null;
        }
    }

    public function getInstanceLabel(): string
    {
        return $this->instanceLabel;
    }

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

    protected function fetchAsBase64(string $url): ?string
    {
        try {
            // Caminho local (storage) ou URL externa
            if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
                $response = Http::timeout(30)->get($url);
                if (! $response->successful()) {
                    return null;
                }
                return base64_encode($response->body());
            }

            if (file_exists($url)) {
                return base64_encode((string) file_get_contents($url));
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Falha ao converter midia para base64: '.$e->getMessage());
            return null;
        }
    }
}
