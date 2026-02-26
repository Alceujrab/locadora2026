<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountsPayableReportExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        try {
            // Get filters from request
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $supplierId = $request->input('supplier_id');
            $branchId = $request->input('branch_id');
            $category = $request->input('category');

            // Build query
            $query = AccountPayable::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($category) {
                $query->where('category', $category);
            }

            $records = $query->with(['supplier', 'branch', 'vehicle'])
                ->orderBy('due_date', 'desc')
                ->get();

            // Calculate totals
            $totals = [
                'total' => $records->sum('amount'),
                'pending' => $records->where('status', 'pendente')->sum('amount'),
                'paid' => $records->where('status', 'pago')->sum('amount'),
                'cancelled' => $records->where('status', 'cancelado')->sum('amount'),
                'overdue' => $records->where('status', 'pendente')
                    ->filter(fn($r) => $r->due_date < now())
                    ->sum('amount'),
            ];

            // Category breakdown
            $byCategory = [];
            foreach (['oficina', 'seguro', 'ipva', 'financiamento', 'aluguel', 'outros'] as $cat) {
                $amount = $records->where('category', $cat)->sum('amount');
                if ($amount > 0) {
                    $byCategory[$cat] = $amount;
                }
            }

            $pdf = Pdf::loadView('reports.accounts-payable-pdf', [
                'records' => $records,
                'totals' => $totals,
                'byCategory' => $byCategory,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'filters' => [
                    'status' => $status,
                    'supplier_id' => $supplierId,
                    'branch_id' => $branchId,
                    'category' => $category,
                ]
            ])
            ->setPaper('a4', 'landscape')
            ->setOption('isPhpEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

            return $pdf->download('Relatório-Contas-Pagar-' . now()->format('d-m-Y') . '.pdf');
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
            $supplierId = $request->input('supplier_id');
            $branchId = $request->input('branch_id');
            $category = $request->input('category');

            // Build query
            $query = AccountPayable::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($supplierId) {
                $query->where('supplier_id', $supplierId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }
            if ($category) {
                $query->where('category', $category);
            }

            $records = $query->with(['supplier', 'branch', 'vehicle'])
                ->orderBy('due_date', 'desc')
                ->get();

            $fileName = 'Relatório-Contas-Pagar-' . now()->format('d-m-Y') . '.xlsx';

            return Excel::download(new \App\Exports\AccountsPayableExport($records), $fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
