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
                ->description('Veiculos cadastrados')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),

            Stat::make('Disponiveis', $disponivel)
                ->description('Prontos para locacao')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Locados', $locado)
                ->description('Em contrato ativo')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary'),

            Stat::make('Manutencao', $manutencao)
                ->description('Em reparo/revisao')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('danger'),

            Stat::make('Reservados', $reservado)
                ->description('Reserva confirmada')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
