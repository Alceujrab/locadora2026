<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunMaintenanceCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica e dispara alertas de manutenção preventiva para toda a frota';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando varredura de Alertas Preventivos de Manutenção...');
        \App\Jobs\DetectMaintenanceNeedsJob::dispatchSync();
        $this->info('Varredura concluída. Verifique os tickets de Help Desk (Suporte) para alertas vigentes.');
    }
}
