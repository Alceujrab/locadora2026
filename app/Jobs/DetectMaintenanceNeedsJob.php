<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DetectMaintenanceNeedsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $alerts = \App\Models\MaintenanceAlert::active()->with('vehicle')->get();

        foreach ($alerts as $alert) {
            if (! $alert->vehicle) {
                continue;
            }

            $needsMaintenance = false;
            $reason = '';

            // Check KM Trigger
            if ($alert->trigger_km > 0 &&
                ($alert->vehicle->current_km - ($alert->last_service_km ?? 0)) >= $alert->trigger_km) {
                $needsMaintenance = true;
                $reason = "Atingiu limite de KM para: {$alert->type}";
            }

            // Check Time Trigger (Days)
            if (! $needsMaintenance && $alert->trigger_days > 0 && $alert->last_service_date) {
                if ($alert->last_service_date->addDays($alert->trigger_days)->isPast()) {
                    $needsMaintenance = true;
                    $reason = "Atingiu limite de Tempo (Dias) para: {$alert->type}";
                }
            }

            // If triggered, open a ticket for the operational team and log it
            if ($needsMaintenance) {
                \App\Models\SupportTicket::create([
                    'title' => "[PREVENTIVA] {$alert->vehicle->title} - {$alert->type}",
                    'description' => "Alerta automático de manutenção gerado pelo sistema.\nMotivo: {$reason}\nPlaca: {$alert->vehicle->license_plate}",
                    'status' => 'open',
                    'priority' => 'high',
                ]);

                // Update last_triggered to avoid spam
                $alert->update(['last_triggered_at' => now()]);
            }
        }
    }
}
