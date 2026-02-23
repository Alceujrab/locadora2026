<?php

declare(strict_types=1);

namespace App\MoonShine\Pages;

use MoonShine\Laravel\Pages\Page;
use MoonShine\UI\Components\Layout\Column;
use MoonShine\UI\Components\Layout\Grid;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Preview;
use MoonShine\UI\Fields\Date;
use App\Models\Invoice;
use Carbon\Carbon;
use MoonShine\UI\Components\Link;

class DefaultReportPage extends Page
{
    public function getBreadcrumbs(): array
    {
        return [
            '#' => $this->title
        ];
    }

    protected string $title = 'Relatório de Inadimplência (Cobrança)';
    protected string $subtitle = 'Relação de Faturas Vencidas';

    public function components(): array
    {
        // Buscar apenas faturas vencidas (Overdue)
        $invoices = Invoice::with(['customer'])
            ->where('status', \App\Enums\InvoiceStatus::OVERDUE)
            ->get()
            ->map(function ($invoice) {
                
                $daysOverdue = $invoice->due_date ? abs((int) Carbon::now()->diffInDays($invoice->due_date)) : 0;
                $totalDebt = $invoice->amount + ($invoice->penalty_amount ?? 0) + ($invoice->interest_amount ?? 0);

                // Criar Link de WhatsApp rápido
                $phone = $invoice->customer->phone ?? '';
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $whatsappLink = $cleanPhone ? "https://wa.me/55{$cleanPhone}?text=" . urlencode("Olá, somos da Elite Locadora. Consta um débito pendente em aberto...") : '#';

                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer' => $invoice->customer->name ?? 'N/A',
                    'contact' => $phone,
                    'due_date' => $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '-',
                    'days_overdue' => $daysOverdue . ' dias',
                    'total_debt' => $totalDebt,
                    'whatsapp_link' => $whatsappLink,
                    'clean_phone' => $cleanPhone
                ];
            })->sortByDesc('total_debt')->values();

        return [
            Grid::make([
                Column::make([
                    TableBuilder::make(items: $invoices)
                        ->fields([
                            Text::make('Fatura', 'invoice_number'),
                            Text::make('Cliente', 'customer'),
                            Text::make('Vencimento', 'due_date')->badge('error'),
                            Text::make('Atraso', 'days_overdue')->badge('warning'),
                            Text::make('Dívida Total', 'total_debt', fn($item) => 'R$ ' . number_format((float)$item['total_debt'], 2, ',', '.'))->badge('error'),
                            Preview::make('Cobrar no WhatsApp', 'contact', function ($item) {
                                if (!$item['clean_phone']) return 'Sem número';
                                return (string) Link::make($item['whatsapp_link'], 'Enviar WhatsApp')
                                    ->icon('chat-bubble-left-ellipsis')
                                    ->blank();
                            }),
                        ])
                        ->withNotFound()
                ])->columnSpan(12)
            ])
        ];
    }
}
