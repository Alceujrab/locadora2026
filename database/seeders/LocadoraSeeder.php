<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\VehicleCategory;
use App\Models\Vehicle;
use App\Models\Customer;
use App\Models\RentalExtra;
use App\Models\Supplier;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\ServiceOrder;
use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Enums\ServiceOrderStatus;
use App\Enums\VehicleStatus;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class LocadoraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('pt_BR');

        // 1. Criar Filial Base
        $branch = Branch::firstOrCreate(
            ['cnpj' => '12.345.678/0001-90'],
            [
                'name' => 'Elite Locadora Matriz',
                'email' => 'contato@elitelocadora.com.br',
                'phone' => '(11) 98888-7777',
                'address_zip' => '01001-000',
                'address_street' => 'Avenida Paulista',
                'address_number' => '1000',
                'address_city' => 'São Paulo',
                'address_state' => 'SP',
                'is_active' => true,
            ]
        );

        // 2. Criar Supliers (Oficinas)
        $suppliers = [];
        for ($i = 1; $i <= 3; $i++) {
            $suppliers[] = Supplier::firstOrCreate([
                'cnpj' => $faker->cnpj(false)
            ], [
                'branch_id' => $branch->id,
                'name' => 'Oficina Mecânica ' . $faker->company,
                'email' => $faker->companyEmail,
                'phone' => $faker->cellphone,
                'type' => 'oficina',
                'is_active' => true,
            ]);
        }

        // 3. Criar Extras
        $extras = [
            ['name' => 'Seguro Proteção Total', 'type' => 'seguro', 'daily_rate' => 50.00],
            ['name' => 'Lavagem Simples', 'type' => 'servico', 'daily_rate' => 45.00],
            ['name' => 'Cadeira de Bebê', 'type' => 'acessorio', 'daily_rate' => 20.00],
        ];

        foreach ($extras as $ex) {
            $ex['branch_id'] = $branch->id;
            RentalExtra::firstOrCreate(['name' => $ex['name']], $ex);
        }

        // 4. Criar Categorias de Veículos
        $catEcon = VehicleCategory::firstOrCreate(['name' => 'Hatch Econômico'], [
            'branch_id' => $branch->id,
            'description' => 'Carros compactos 1.0',
            'daily_rate' => 120.00,
            'weekly_rate' => 700.00,
            'monthly_rate' => 2500.00,
            'km_included' => 100,
            'km_rate' => 0.50,
        ]);

        $catSedan = VehicleCategory::firstOrCreate(['name' => 'Sedan Executivo'], [
            'branch_id' => $branch->id,
            'description' => 'Carros confortáveis com porta malas',
            'daily_rate' => 250.00,
            'weekly_rate' => 1500.00,
            'monthly_rate' => 5000.00,
            'km_included' => 150,
            'km_rate' => 1.00,
        ]);

        $catSuv = VehicleCategory::firstOrCreate(['name' => 'SUV Premium'], [
            'branch_id' => $branch->id,
            'description' => 'Veículos altos e espaçosos',
            'daily_rate' => 400.00,
            'weekly_rate' => 2500.00,
            'monthly_rate' => 8500.00,
            'km_included' => 200,
            'km_rate' => 1.50,
        ]);

        // 5. Criar Veículos (15 unidades reais)
        $vehicleData = [
            ['cat' => $catEcon->id, 'plate' => 'ABC1D23', 'brand' => 'Fiat', 'model' => 'Argo 1.0', 'year' => 2022, 'color' => 'Branco'],
            ['cat' => $catEcon->id, 'plate' => 'DEF4G56', 'brand' => 'Hyundai', 'model' => 'HB20', 'year' => 2023, 'color' => 'Prata'],
            ['cat' => $catEcon->id, 'plate' => 'GHI7J89', 'brand' => 'Chevrolet', 'model' => 'Onix', 'year' => 2024, 'color' => 'Preto'],
            ['cat' => $catEcon->id, 'plate' => 'JKL0M12', 'brand' => 'Renault', 'model' => 'Kwid', 'year' => 2022, 'color' => 'Vermelho'],
            ['cat' => $catEcon->id, 'plate' => 'MNO3P45', 'brand' => 'Peugeot', 'model' => '208', 'year' => 2023, 'color' => 'Cinza'],

            ['cat' => $catSedan->id, 'plate' => 'PQR6S78', 'brand' => 'Toyota', 'model' => 'Corolla', 'year' => 2022, 'color' => 'Prata'],
            ['cat' => $catSedan->id, 'plate' => 'STU9V01', 'brand' => 'Honda', 'model' => 'Civic', 'year' => 2021, 'color' => 'Preto'],
            ['cat' => $catSedan->id, 'plate' => 'VWX2Y34', 'brand' => 'Nissan', 'model' => 'Versa', 'year' => 2023, 'color' => 'Branco'],
            ['cat' => $catSedan->id, 'plate' => 'YZA5B67', 'brand' => 'Chevrolet', 'model' => 'Cruze', 'year' => 2022, 'color' => 'Cinza'],
            ['cat' => $catSedan->id, 'plate' => 'BCD8E90', 'brand' => 'VW', 'model' => 'Virtus', 'year' => 2024, 'color' => 'Azul'],

            ['cat' => $catSuv->id, 'plate' => 'EFG1H23', 'brand' => 'Jeep', 'model' => 'Compass', 'year' => 2023, 'color' => 'Branco'],
            ['cat' => $catSuv->id, 'plate' => 'HIJ4K56', 'brand' => 'Hyundai', 'model' => 'Creta', 'year' => 2024, 'color' => 'Cinza'],
            ['cat' => $catSuv->id, 'plate' => 'KLM7N89', 'brand' => 'VW', 'model' => 'T-Cross', 'year' => 2022, 'color' => 'Preto'],
            ['cat' => $catSuv->id, 'plate' => 'NOP0Q12', 'brand' => 'Toyota', 'model' => 'Corolla Cross', 'year' => 2024, 'color' => 'Prata'],
            ['cat' => $catSuv->id, 'plate' => 'RST3U45', 'brand' => 'Jeep', 'model' => 'Renegade', 'year' => 2023, 'color' => 'Verde'],
        ];

        // Limpa pra não duplicar excessivamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('invoices')->truncate();
        DB::table('service_orders')->truncate();
        DB::table('contracts')->truncate();
        DB::table('vehicles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $vehicles = [];
        foreach ($vehicleData as $v) {
            $vehicles[] = Vehicle::create([
                'branch_id' => $branch->id,
                'category_id' => $v['cat'],
                'plate' => $v['plate'],
                'chassis' => strtoupper(Str::random(17)),
                'renavam' => $faker->numerify('###########'),
                'brand' => $v['brand'],
                'model' => $v['model'],
                'year_manufacture' => $v['year'],
                'year_model' => $v['year'],
                'color' => $v['color'],
                'fuel' => 'flex',
                'transmission' => in_array($v['brand'], ['Jeep', 'Toyota', 'Honda']) ? 'automatico' : 'manual',
                'mileage' => rand(10000, 80000),
                'status' => VehicleStatus::AVAILABLE,
                'purchase_date' => now()->subMonths(rand(6, 36)),
            ]);
        }

        // 6. Criar Clientes (10 Clientes, PF e PJ mixados)
        $customers = [];
        for ($i = 0; $i < 10; $i++) {
            $isPf = $i % 2 === 0;
            $customers[] = Customer::firstOrCreate(
                ['email' => $faker->unique()->safeEmail],
                [
                    'branch_id' => $branch->id,
                    'type' => $isPf ? 'pf' : 'pj',
                    'name' => $isPf ? $faker->name : $faker->company,
                    'cpf_cnpj' => $isPf ? static::generateCpf() : static::generateCnpj(),
                    'phone' => $faker->cellphone,
                    'cnh_number' => $isPf ? $faker->numerify('###########') : null,
                    'cnh_category' => 'B',
                    'cnh_expiry' => now()->addYears(rand(1, 5)),
                    'address_zip' => $faker->postcode,
                    'address_street' => $faker->streetName,
                    'address_number' => rand(10, 999),
                    'address_city' => $faker->city,
                    'address_state' => 'SP',
                ]
            );
        }

        // 7. Simular Histórico de Contratos e Faturas (Para o Dashboard/Relatórios)

        // 7.1 Contratos Antigos (Aumentam Rentabilidade)
        for ($i = 0; $i < 15; $i++) {
            $customer = $faker->randomElement($customers);
            $vehicle = $faker->randomElement($vehicles);
            $days = rand(3, 15);
            $pickup = Carbon::now()->subMonths(rand(1, 12))->subDays(rand(1, 20));
            $return = (clone $pickup)->addDays($days);
            
            $total = $days * $vehicle->category->daily_rate;

            $contract = Contract::create([
                'contract_number' => 'LOC-' . rand(1000, 9999) . '-' . Str::upper(Str::random(4)),
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'status' => ContractStatus::FINISHED, // Status Finalizado
                'pickup_date' => $pickup,
                'return_date' => $return,
                'actual_return_date' => clone $return,
                'pickup_mileage' => $vehicle->mileage - rand(500, 2000),
                'return_mileage' => $vehicle->mileage - rand(100, 400),
                'total' => $total,
            ]);

            // Gerar Fatura Paga para este contrato antigo
            Invoice::create([
                'invoice_number' => 'INV-' . rand(10000, 99999),
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'contract_id' => $contract->id,
                'amount' => $total,
                'due_date' => clone $return,
                'status' => InvoiceStatus::PAID, // Receita OK
                'created_at' => clone $pickup,
                'updated_at' => clone $pickup,
                'notes' => "Locação #{$contract->id} ({$days} diárias)",
            ]);

            // Gerar Manutenção Paga (Despesa) para abater no carro
            if (rand(1, 10) > 6) { // 40% de chance de ter tido OS
                ServiceOrder::create([
                    'branch_id' => $branch->id,
                    'vehicle_id' => $vehicle->id,
                    'supplier_id' => collect($suppliers)->random()->id,
                    'status' => ServiceOrderStatus::COMPLETED,
                    'type' => 'preventiva',
                    'opened_at' => clone $pickup->subDays(2),
                    'completed_at' => clone $pickup->subDays(1),
                    'total' => rand(200, 1500),
                    'description' => 'Revisão periódica (Auto-gerada)',
                ]);
            }
        }

        // 7.2 Inadimplência (Faturas Atrasadas - Sem contrato amarrado para simplificar)
        for ($j = 0; $j < 8; $j++) {
            $customer = collect($customers)->random();
            $issueDate = Carbon::now()->subDays(rand(15, 60));
            $dueDate = (clone $issueDate)->addDays(5);
            $amount = rand(500, 3500);

            Invoice::create([
                'invoice_number' => 'INV-' . rand(10000, 99999),
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'amount' => $amount,
                'due_date' => $dueDate,
                'status' => InvoiceStatus::OVERDUE, // Status Atrasado!
                'created_at' => $issueDate,
                'updated_at' => $issueDate,
                'penalty_amount' => $amount * 0.02, // 2% multa base
                'interest_amount' => $amount * 0.05, // 5% juros acumulado fictício
                'notes' => "Mensalidade/Locação (Inadimplente - Auto-gerada) - Gerada via Seeder",
            ]);
        }

        // 7.3 Contratos Em Andamento (Frotas alugadas hoje)
        for ($k = 0; $k < 5; $k++) {
            $customer = collect($customers)->random();
            $vehicle = collect($vehicles)->where('status', VehicleStatus::AVAILABLE)->random();
            
            // Marca o veículo como alugado "dummy" pro seeder
            $vehicle->update(['status' => VehicleStatus::RENTED]);

            $days = rand(5, 30);
            $pickup = Carbon::now()->subDays(rand(1, 4)); // Pegou dias atrás
            $return = Carbon::now()->addDays($days); // Devolve no futuro
            
            Contract::create([
                'contract_number' => 'LOC-' . rand(1000, 9999) . '-' . Str::upper(Str::random(4)),
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'vehicle_id' => $vehicle->id,
                'status' => ContractStatus::ACTIVE,
                'pickup_date' => $pickup,
                'return_date' => $return,
                'pickup_mileage' => $vehicle->mileage,
                'total' => $days * $vehicle->category->daily_rate,
            ]);
        }
    }

    private static function generateCpf(): string
    {
        return rand(111, 999) . '.' . rand(111, 999) . '.' . rand(111, 999) . '-' . rand(11, 99);
    }
    private static function generateCnpj(): string
    {
        return rand(11, 99) . '.' . rand(111, 999) . '.' . rand(111, 999) . '/0001-' . rand(11, 99);
    }
}
