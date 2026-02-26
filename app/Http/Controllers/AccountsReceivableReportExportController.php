<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountsReceivableReportExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        try {
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            $query = AccountReceivable::whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($status) $query->where('status', $status);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($branchId) $query->where('branch_id', $branchId);

            $records = $query->with(['customer', 'invoice', 'branch'])->orderBy('due_date', 'desc')->get();

            $totals = [
                'total_amount' => $records->sum('amount'),
                'total_paid' => $records->sum('paid_amount'),
                'total_remaining' => $records->sum(function($r) { return $r->amount - $r->paid_amount; }),
                'pending' => $records->where('status', 'pendente')->sum('amount'),
                'partial' => $records->where('status', 'parcial')->sum('amount'),
                'received' => $records->where('status', 'recebido')->sum('amount'),
                'delinquent' => $records->where('status', 'inadimplente')->sum('amount'),
            ];

            $pdf = Pdf::loadView('reports.accounts-receivable-pdf', [
                'records' => $records,
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

            return $pdf->download('Relatório-Contas-Receber-' . now()->format('d-m-Y') . '.pdf');
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

            $query = AccountReceivable::whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($status) $query->where('status', $status);
            if ($customerId) $query->where('customer_id', $customerId);
            if ($branchId) $query->where('branch_id', $branchId);

            $records = $query->with(['customer', 'invoice', 'branch'])->orderBy('due_date', 'desc')->get();

            $fileName = 'Relatório-Contas-Receber-' . now()->format('d-m-Y') . '.xlsx';
            $filePath = storage_path('app/' . $fileName);

            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile($filePath);

            $headerStyle = (new Style())->setFontBold()->setFontColor(Color::WHITE)->setBackgroundColor('10B981');
            $writer->addRow(Row::fromValues([
                'Descrição', 'Cliente', 'Fatura', 'Data de Vencimento',
                'Valor Total', 'Valor Recebido', 'Saldo', 'Status',
                'Método de Pagamento', 'Data de Recebimento',
            ], $headerStyle));

            foreach ($records as $record) {
                $writer->addRow(Row::fromValues([
                    $record->description,
                    $record->customer->name ?? 'N/A',
                    $record->invoice->invoice_number ?? '-',
                    $record->due_date->format('d/m/Y'),
                    number_format($record->amount, 2, ',', '.'),
                    number_format($record->paid_amount, 2, ',', '.'),
                    number_format($record->amount - $record->paid_amount, 2, ',', '.'),
                    ucfirst($record->status),
                    $record->payment_method ?? '-',
                    $record->received_at ? $record->received_at->format('d/m/Y') : '-',
                ]));
            }

            $writer->close();

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
