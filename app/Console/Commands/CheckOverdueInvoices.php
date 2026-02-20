<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;

class CheckOverdueInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica faturas vencidas diariamente e aplica multas e juros';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando verificação de faturas vencidas...");
        
        $invoices = Invoice::whereIn('status', [InvoiceStatus::OPEN, InvoiceStatus::OVERDUE])
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();
            
        $count = 0;

        foreach ($invoices as $invoice) {
            $charges = $invoice->calculatePenaltyAndInterest();
            
            $invoice->update([
                'status' => InvoiceStatus::OVERDUE,
                'penalty_amount' => $charges['penalty'],
                'interest_amount' => $charges['interest'],
            ]);
            $count++;
        }

        $this->info("{$count} fatura(s) verificada(s) e atualizada(s) com sucesso.");
    }
}
