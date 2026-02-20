<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\EvolutionApiService;
use Illuminate\Support\Facades\Log;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $phoneNumber,
        public string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(EvolutionApiService $evolutionService): void
    {
        if (empty($this->phoneNumber)) {
            Log::warning("SendWhatsAppMessageJob cancelado: Telefone vazio.");
            return;
        }

        $evolutionService->sendText($this->phoneNumber, $this->message);
    }
}
