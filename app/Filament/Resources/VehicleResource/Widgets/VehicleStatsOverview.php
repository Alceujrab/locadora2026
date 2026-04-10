<?php

namespace App\Filament\Resources\VehicleResource\Widgets;

use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VehicleStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $total = Vehicle::count();
        $disponivel = Vehicle::where('status', 'disponivel')->count();
        $locado = Vehicle::where('status', 'locado')->count();
        $manutencao = Vehicle::where('status', 'manutencao')->count();
        $reservado = Vehicle::where('status', 'reservado')->count();
        $inativo = Vehicle::where('status', 'inativo')->count();

        return [
            Stat::make('Total Frota', $total)
                ->description('Veículos cadastrados')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Disponíveis', $disponivel)
                ->description('Prontos para locação')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Locados', $locado)
                ->description('Em contrato ativo')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),

            Stat::make('Manutenção', $manutencao)
                ->description('Em reparo/revisão')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),

            Stat::make('Reservados', $reservado)
                ->description('Reserva confirmada')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
