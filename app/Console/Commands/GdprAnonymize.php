<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

class GdprAnonymize extends Command
{
    protected $signature = 'gdpr:anonymize {--years=5 : Number of years of inactivity before anonymization} {--dry-run : Preview without executing}';

    protected $description = 'Anonimiza dados pessoais de clientes inativos (LGPD). Remove nome, CPF, telefone e email.';

    public function handle(): int
    {
        $years = (int) $this->option('years');
        $dryRun = $this->option('dry-run');
        $cutoffDate = now()->subYears($years);

        $query = Customer::where('updated_at', '<', $cutoffDate)
            ->where('name', '!=', 'Anonimizado')
            ->whereDoesntHave('contracts', function ($q) use ($cutoffDate) {
                $q->where('end_date', '>=', $cutoffDate);
            });

        $count = $query->count();

        if ($count === 0) {
            $this->info('Nenhum cliente elegível para anonimização encontrado.');
            return self::SUCCESS;
        }

        $this->warn("Encontrados {$count} clientes inativos há mais de {$years} anos.");

        if ($dryRun) {
            $this->info('[DRY-RUN] Os seguintes clientes seriam anonimizados:');
            $query->each(function ($customer) {
                $this->line(" - ID: {$customer->id} | Nome: {$customer->name} | Últ. Atualização: {$customer->updated_at}");
            });
            return self::SUCCESS;
        }

        if (!$this->confirm('Confirma a anonimização? Esta ação é IRREVERSÍVEL.')) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $anonymized = 0;
        $query->chunkById(100, function ($customers) use (&$anonymized) {
            foreach ($customers as $customer) {
                $customer->update([
                    'name' => 'Anonimizado',
                    'email' => 'anonimizado_' . $customer->id . '@lgpd.local',
                    'cpf_cnpj' => Hash::make($customer->cpf_cnpj ?? ''),
                    'phone' => null,
                    'whatsapp' => null,
                    'address' => null,
                    'address_number' => null,
                    'address_complement' => null,
                    'neighborhood' => null,
                ]);
                $anonymized++;
            }
        });

        $this->info("✅ {$anonymized} clientes anonimizados com sucesso.");

        return self::SUCCESS;
    }
}
