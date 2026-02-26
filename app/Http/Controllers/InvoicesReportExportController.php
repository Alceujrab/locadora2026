<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoicesReportExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        try {
            // Get filters from request
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            // Build query
            $query = Invoice::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $invoices = $query->with(['customer', 'contract', 'branch'])
                ->orderBy('due_date', 'desc')
                ->get();

            // Calculate totals
            $totals = [
                'total' => $invoices->sum('total'),
                'open' => $invoices->where('status', InvoiceStatus::OPEN->value)->sum('total'),
                'overdue' => $invoices->where('status', InvoiceStatus::OVERDUE->value)->sum('total'),
                'paid' => $invoices->where('status', InvoiceStatus::PAID->value)->sum('total'),
                'cancelled' => $invoices->where('status', InvoiceStatus::CANCELLED->value)->sum('total'),
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
            // Get filters from request
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            // Build query
            $query = Invoice::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $invoices = $query->with(['customer', 'contract', 'branch'])
                ->orderBy('due_date', 'desc')
                ->get();

            $fileName = 'Relatório-Faturas-' . now()->format('d-m-Y') . '.xlsx';

            return Excel::download(new \App\Exports\InvoicesExport($invoices), $fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
