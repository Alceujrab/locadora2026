<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
#[\MoonShine\MenuManager\Attributes\SkipMenu]

class Dashboard extends Page
{
    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->getTitle()
        ];
    }

    public function getTitle(): string
    {
        return $this->title ?: 'Dashboard';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Faturamento Mensal (Invoices pagas no mês atual)
        $revenue = \App\Models\Invoice::where('status', \App\Enums\InvoiceStatus::PAID)
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('total');

        // Contas a Pagar (Pendentes do mês)
        $payable = \App\Models\AccountPayable::where('status', 'pendente')
            ->whereMonth('due_date', $currentMonth)
            ->whereYear('due_date', $currentYear)
            ->sum('amount');

        // Frota: Totais e Locados
        $totalVehicles = \App\Models\Vehicle::count();
        $rentedVehicles = \App\Models\Vehicle::where('status', \App\Enums\VehicleStatus::RENTED)->count();
        $occupancyRate = $totalVehicles > 0 ? round(($rentedVehicles / $totalVehicles) * 100) : 0;

        // Contratos Ativos
        $activeContracts = \App\Models\Contract::where('status', \App\Enums\ContractStatus::ACTIVE)->count();

		return [
            \MoonShine\UI\Components\Layout\Grid::make([
                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Faturamento Líquido (Mês)')
                        ->value('R$ ' . number_format($revenue, 2, ',', '.'))
                        ->icon('banknotes')
                        ->customAttributes(['class' => 'text-success']),
                ])->columnSpan(3),

                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('A Pagar Pêndente (Mês)')
                        ->value('R$ ' . number_format($payable, 2, ',', '.'))
                        ->icon('arrow-trending-down')
                        ->customAttributes(['class' => 'text-error']),
                ])->columnSpan(3),

                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Taxa de Ocupação Atual')
                        ->value($occupancyRate . ' %')
                        ->progress((int)$occupancyRate)
                        ->icon('truck'),
                ])->columnSpan(3),

                \MoonShine\UI\Components\Layout\Column::make([
                    \MoonShine\UI\Components\Metrics\Wrapped\ValueMetric::make('Contratos Ativos')
                        ->value($activeContracts)
                        ->icon('document-text'),
                ])->columnSpan(3),
            ])
        ];
	}
}
