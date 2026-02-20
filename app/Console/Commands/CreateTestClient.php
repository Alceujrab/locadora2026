<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestClient extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:test-client';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um usuário cliente de teste para acesso ao Portal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = 'cliente@test.com';
        $password = 'senha123';
        
        $branch = \App\Models\Branch::firstOrCreate(
            ['name' => 'Sede Principal'],
            [
                'phone' => '11999999999',
                'active' => true,
                'matrix' => true,
                'cnpj' => '00.000.000/0001-00'
            ]
        );

        $user = \App\Models\User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Senhor Cliente Teste',
                'password' => \Illuminate\Support\Facades\Hash::make($password),
                'branch_id' => $branch->id
            ]
        );

        // Forces password reset if it already existed
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($password)]);

        if (!$user->hasRole('cliente')) {
            $user->assignRole('cliente');
        }

        $customer = \App\Models\Customer::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Senhor Cliente Teste',
                'cpf_cnpj' => '11122233344',
                'phone' => '11999999999',
                'type' => 'pf',
                'branch_id' => $branch->id,
            ]
        );

        $customer->update(['user_id' => $user->id]);

        $this->info("Cliente de Teste Criado com Sucesso!");
        $this->table(['E-mail', 'Senha', 'Role'], [[$user->email, $password, 'cliente']]);
    }
}
