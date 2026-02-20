<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Metrics\Wrapped\ValueMetric;
use App\Models\Invoice;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Enums\InvoiceStatus;

class CashFlowPage extends Page
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
        return $this->title ?: 'CashFlowPage';
    }

    /**
     * @return list<ComponentContract>
     */
    protected function actionButtons(): iterable
    {
        return [
            \MoonShine\UI\Components\ActionButton::make('Exportar CSV (Mês Atual)', route('export.cashflow'))
                ->icon('document-arrow-down')
                ->success()
                ->blank(),
        ];
    }

    /**
     * @return list<ComponentContract>
     */
    protected function components(): iterable
	{
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Entradas Totais do mês
        $invoicesTotal = Invoice::where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('total');

        $receivablesTotal = AccountReceivable::where('status', 'pago')
            ->whereMonth('received_at', $currentMonth)
            ->whereYear('received_at', $currentYear)
            ->sum('amount');

        $totalIn = $invoicesTotal + $receivablesTotal;

        // Saídas (Despesas Manuais Pagas)
        $totalOut = AccountPayable::where('status', 'pago')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->sum('amount');

        $balance = $totalIn - $totalOut;

		return [
            Grid::make([
                Column::make([
                    ValueMetric::make('Total de Entradas (Mês)')
                        ->value(number_format($totalIn, 2, ',', '.'))
                        ->icon('arrow-trending-up')
                        ->customAttributes(['class' => 'text-success']),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Total de Saídas (Mês)')
                        ->value(number_format($totalOut, 2, ',', '.'))
                        ->icon('arrow-trending-down')
                        ->customAttributes(['class' => 'text-error']),
                ])->columnSpan(4),

                Column::make([
                    ValueMetric::make('Saldo Líquido Atual')
                        ->value(number_format($balance, 2, ',', '.'))
                        ->icon('banknotes')
                        ->customAttributes(['class' => $balance >= 0 ? 'text-success' : 'text-error']),
                ])->columnSpan(4),

            ]),
        ];
	}
}
