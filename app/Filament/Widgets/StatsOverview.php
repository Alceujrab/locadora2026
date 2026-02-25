<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeVehicles = Vehicle::where('status', 'available')->count();
        $activeContracts = Contract::whereIn('status', ['active', 'signed'])->count();
        $totalCustomers = Customer::count();

        return [
            Stat::make('Veículos Disponíveis', $activeVehicles)
                ->description('Prontos para locação')
                ->descriptionIcon('heroicon-m-truck')
                ->color('success'),

            Stat::make('Contratos Ativos', $activeContracts)
                ->description('Em andamento no momento')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Total de Clientes', $totalCustomers)
                ->description('Cadastrados na base')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
