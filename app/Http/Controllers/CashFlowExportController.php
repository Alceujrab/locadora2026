<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Invoice;

class CashFlowExportController extends Controller
{
    public function exportCsv()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $invoices = Invoice::where('status', InvoiceStatus::PAID)
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->get();

        $receivables = AccountReceivable::where('status', 'pago')
            ->whereMonth('received_at', $currentMonth)
            ->whereYear('received_at', $currentYear)
            ->get();

        $payables = AccountPayable::where('status', 'pago')
            ->whereMonth('paid_at', $currentMonth)
            ->whereYear('paid_at', $currentYear)
            ->get();

        $filename = "fluxo_caixa_{$currentMonth}_{$currentYear}.csv";
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = ['Tipo', 'Descricao', 'Valor (R$)', 'Data'];

        $callback = function () use ($invoices, $receivables, $payables, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($invoices as $item) {
                fputcsv($file, ['Entrada (Fatura)', "Fatura #{$item->invoice_number}", $item->total, $item->paid_at->format('d/m/Y')], ';');
            }
            foreach ($receivables as $item) {
                fputcsv($file, ['Entrada (Recebível)', $item->description, $item->amount, $item->received_at->format('d/m/Y')], ';');
            }
            foreach ($payables as $item) {
                fputcsv($file, ['Saida (A Pagar)', $item->description, $item->amount, $item->paid_at->format('d/m/Y')], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
