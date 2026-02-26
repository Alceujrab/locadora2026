<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoicesReportExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            $query = Invoice::whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($status) $query->where('status', $status);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($branchId) $query->where('branch_id', $branchId);

            $invoices = $query->with(['customer', 'contract', 'branch'])->orderBy('due_date', 'desc')->get();

            // Compare using enum objects (status is cast to InvoiceStatus)
            $totals = [
                'total' => $invoices->sum('total'),
                'open' => $invoices->where('status', InvoiceStatus::OPEN)->sum('total'),
                'overdue' => $invoices->where('status', InvoiceStatus::OVERDUE)->sum('total'),
                'paid' => $invoices->where('status', InvoiceStatus::PAID)->sum('total'),
                'cancelled' => $invoices->where('status', InvoiceStatus::CANCELLED)->sum('total'),
            ];

            $pdf = Pdf::loadView('reports.invoices-pdf', [
                'invoices' => $invoices,
                'totals' => $totals,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'filters' => [
                    'status' => $status,
                    'customer_id' => $customerId,
                    'branch_id' => $branchId,
                ]
            ])
            ->setPaper('a4', 'landscape')
            ->setOption('isPhpEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

            return $pdf->download('Relatório-Faturas-' . now()->format('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            $query = Invoice::whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($status) $query->where('status', $status);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($branchId) $query->where('branch_id', $branchId);

            $invoices = $query->with(['customer', 'contract', 'branch'])->orderBy('due_date', 'desc')->get();

            $fileName = 'Relatório-Faturas-' . now()->format('d-m-Y') . '.xlsx';
            $filePath = storage_path('app/' . $fileName);

            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile($filePath);

            // Header row with style
            $headerStyle = (new Style())->setFontBold()->setFontColor(Color::WHITE)->setBackgroundColor('3B82F6');
            $writer->addRow(Row::fromValues([
                'Número da Fatura', 'Cliente', 'Contrato', 'Data de Vencimento',
                'Status', 'Valor Total', 'Data de Pagamento', 'Método de Pagamento',
            ], $headerStyle));

            // Data rows
            foreach ($invoices as $invoice) {
                $statusVal = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;
                $writer->addRow(Row::fromValues([
                    $invoice->invoice_number,
                    $invoice->customer->name ?? 'N/A',
                    $invoice->contract->number ?? 'N/A',
                    $invoice->due_date->format('d/m/Y'),
                    ucfirst($statusVal),
                    number_format($invoice->total, 2, ',', '.'),
                    $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : '-',
                    $invoice->payment_method ?? '-',
                ]));
            }

            $writer->close();

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
