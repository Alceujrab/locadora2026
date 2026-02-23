<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Text;
use App\Models\Vehicle;
use MoonShine\Contracts\UI\ComponentContract;


class FleetProfitabilityPage extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->title
        ];
    }

    protected string $title = 'Rentabilidade da Frota';
    protected string $subtitle = 'Análise de Receitas vs Custos por Veículo';

    /**
     * @return list<ComponentContract>
     */
    public function components(): array
    {
        // Calcular rentabilidade por veículo
        $vehicles = Vehicle::with(['contracts', 'serviceOrders'])->get()->map(function ($vehicle) {
            
            // Faturamento Bruto: Soma total dos contratos Pagos/Finalizados associados
            // Em tese seria mais exato somar Invoices pagas amarradas, mas pra simulação rápida usaremos Contratos Terminados
            $grossRevenue = $vehicle->contracts()
                ->where('status', \App\Enums\ContractStatus::FINISHED)
                ->sum('total');

            // Custo de Manutenção: Soma das O.S concluídas e pagas daquele veículo
            $maintenanceCost = $vehicle->serviceOrders()
                ->where('status', \App\Enums\ServiceOrderStatus::COMPLETED)
                ->sum('total');

            $netProfit = $grossRevenue - $maintenanceCost;
            $profitMargin = $grossRevenue > 0 ? ($netProfit / $grossRevenue) * 100 : 0;

            return [
                'id' => $vehicle->id,
                'plate' => $vehicle->plate,
                'brand_model' => "{$vehicle->brand} {$vehicle->model}",
                'status' => $vehicle->status,
                'gross_revenue' => $grossRevenue,
                'maintenance_cost' => $maintenanceCost,
                'net_profit' => $netProfit,
                'profit_margin' => number_format($profitMargin, 1, ',', '.') . '%',
            ];
        })->sortByDesc('net_profit')->values();

        return [
            Grid::make([
                Column::make([
                    TableBuilder::make(items: $vehicles)
                        ->fields([
                            Text::make('ID', 'id'),
                            Text::make('Placa', 'plate'),
                            Text::make('Veículo', 'brand_model'),
                            Text::make('Status', 'status')->badge('info'),
                            Text::make('Receita Bruta', 'gross_revenue', fn($item) => 'R$ ' . number_format((float)$item['gross_revenue'], 2, ',', '.'))->badge('success'),
                            Text::make('Custo (Manutenção)', 'maintenance_cost', fn($item) => 'R$ ' . number_format((float)$item['maintenance_cost'], 2, ',', '.'))->badge('error'),
                            Text::make('Lucro Líquido', 'net_profit', fn($item) => 'R$ ' . number_format((float)$item['net_profit'], 2, ',', '.'))->badge(fn($val, $item) => $item['net_profit'] >= 0 ? 'success' : 'error'),
                            Text::make('Margem', 'profit_margin'),
                        ])
                        ->withNotFound()
                ])->columnSpan(12)
            ])
        ];
    }
}
