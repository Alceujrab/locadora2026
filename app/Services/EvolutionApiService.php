<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvolutionApiService
{
    protected string $baseUrl;
    protected string $instanceName;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('EVOLUTION_API_URL', ''), '/');
        $this->instanceName = env('EVOLUTION_INSTANCE_NAME', '');
        $this->apiKey = env('EVOLUTION_API_KEY', '');
    }

    /**
     * Envia uma mensagem de texto via Evolution API
     *
     * @param string $number Telefone com código do país (ex: 5511999999999)
     * @param string $text Conteúdo da mensagem
     * @return array|null
     */
    public function sendText(string $number, string $text): ?array
    {
        if (empty($this->baseUrl) || empty($this->instanceName) || empty($this->apiKey)) {
            Log::warning('Configurações da Evolution API incompletas no .env.');
            return null;
        }

        try {
            $endpoint = "{$this->baseUrl}/message/sendText/{$this->instanceName}";
            
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/json'
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
                'response' => $response->body()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error("Exceção disparando WhatsApp: " . $e->getMessage());
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
            $number = '55' . $number;
        }

        return $number;
    }
}
