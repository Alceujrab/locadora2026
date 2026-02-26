<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountsReceivableReportExportController extends Controller
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
            $query = AccountReceivable::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $records = $query->with(['customer', 'invoice', 'branch'])
                ->orderBy('due_date', 'desc')
                ->get();

            // Calculate totals
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
            // Get filters from request
            $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->subDays(90);
            $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->addDays(30);
            $status = $request->input('status');
            $customerId = $request->input('customer_id');
            $branchId = $request->input('branch_id');

            // Build query
            $query = AccountReceivable::whereBetween('due_date', [$dateFrom, $dateTo]);

            if ($status) {
                $query->where('status', $status);
            }
            if ($customerId) {
                $query->where('customer_id', $customerId);
            }
            if ($branchId) {
                $query->where('branch_id', $branchId);
            }

            $records = $query->with(['customer', 'invoice', 'branch'])
                ->orderBy('due_date', 'desc')
                ->get();

            $fileName = 'Relatório-Contas-Receber-' . now()->format('d-m-Y') . '.xlsx';

            return Excel::download(new \App\Exports\AccountsReceivableExport($records), $fileName);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
