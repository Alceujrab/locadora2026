<?php

namespace App\Filament\Pages;

use App\Models\AccountPayable;
use App\Models\Branch;
use App\Models\Supplier;
use Filament\Pages\Page;
use Carbon\Carbon;

class AccountsPayableReportPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static string|\UnitEnum|null $navigationGroup = 'Relatorios';
    protected static ?int $navigationSort = 7;
    protected string $view = 'filament.pages.accounts-payable-report-page';
    protected static ?string $title = 'Relatório de Contas a Pagar';
    protected static ?string $slug = 'relatorios/contas-pagar';

    public function getViewData(): array
    {
        try {
            // Get filters from request
            $dateFrom = request('date_from') ? Carbon::parse(request('date_from')) : now()->subDays(90);
            $dateTo = request('date_to') ? Carbon::parse(request('date_to')) : now()->addDays(30);
            $status = request('status');
            $supplierId = request('supplier_id');
            $branchId = request('branch_id');
            $category = request('category');

            // Base query with date range
            $baseQuery = function() use ($dateFrom, $dateTo, $status, $supplierId, $branchId, $category) {
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

                return $query;
            };

            // Get records with relationships
            $records = $baseQuery()
                ->with(['supplier', 'branch', 'vehicle'])
                ->orderBy('due_date', 'desc')
                ->paginate(50);

            // Clone for aggregate calculations
            $query = $baseQuery();

            // Calculate KPIs
            $totalCount = $query->clone()->count();
            $totalAmount = $query->clone()->sum('amount');

            $pendingAmount = $query->clone()->where('status', 'pendente')->sum('amount');
            $pendingCount = $query->clone()->where('status', 'pendente')->count();

            $paidAmount = $query->clone()->where('status', 'pago')->sum('amount');
            $paidCount = $query->clone()->where('status', 'pago')->count();

            $cancelledAmount = $query->clone()->where('status', 'cancelado')->sum('amount');
            $cancelledCount = $query->clone()->where('status', 'cancelado')->count();

            // Overdue (pending and past due_date)
            $overdueAmount = $query->clone()
                ->where('status', 'pendente')
                ->where('due_date', '<', now())
                ->sum('amount');
            $overdueCount = $query->clone()
                ->where('status', 'pendente')
                ->where('due_date', '<', now())
                ->count();

            // Prepare chart data
            $statusData = [
                'labels' => ['Pendente', 'Pago', 'Cancelado'],
                'data' => [$pendingCount, $paidCount, $cancelledCount],
                'colors' => ['#ef4444', '#10b981', '#6b7280']
            ];

            // Category breakdown
            $categories = ['oficina', 'seguro', 'ipva', 'financiamento', 'aluguel', 'outros'];
            $categoryData = [];
            $categoryLabels = [];
            $categoryColors = ['#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b', '#06b6d4', '#6b7280'];

            foreach ($categories as $index => $cat) {
                $amount = $query->clone()->where('category', $cat)->sum('amount');
                if ($amount > 0) {
                    $categoryLabels[] = ucfirst($cat);
                    $categoryData[] = $amount;
                }
            }

            // Monthly trend data
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = AccountPayable::whereYear('due_date', $month->year)
                    ->whereMonth('due_date', $month->month)
                    ->count();
                $monthlyData[] = [
                    'month' => $month->format('M/y'),
                    'count' => $count
                ];
            }

            // Get branches and suppliers for filters
            $branches = Branch::all();
            $suppliers = Supplier::all();

            return [
                'records' => $records,
                'totalCount' => $totalCount,
                'totalAmount' => $totalAmount,
                'pendingCount' => $pendingCount,
                'pendingAmount' => $pendingAmount,
                'paidCount' => $paidCount,
                'paidAmount' => $paidAmount,
                'cancelledCount' => $cancelledCount,
                'cancelledAmount' => $cancelledAmount,
                'overdueCount' => $overdueCount,
                'overdueAmount' => $overdueAmount,
                'statusData' => $statusData,
                'categoryData' => [
                    'labels' => $categoryLabels,
                    'data' => $categoryData,
                    'colors' => array_slice($categoryColors, 0, count($categoryLabels))
                ],
                'monthlyData' => $monthlyData,
                'branches' => $branches,
                'suppliers' => $suppliers,
                'categories' => collect($categories)->mapWithKeys(function($cat) { 
                    return [$cat => ucfirst($cat)];
                }),
                'filters' => [
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to'),
                    'status' => $status,
                    'supplier_id' => $supplierId,
                    'branch_id' => $branchId,
                    'category' => $category,
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('AccountsPayableReportPage error: ' . $e->getMessage());

            return [
                'records' => collect(),
                'totalCount' => 0,
                'totalAmount' => 0,
                'pendingCount' => 0,
                'pendingAmount' => 0,
                'paidCount' => 0,
                'paidAmount' => 0,
                'cancelledCount' => 0,
                'cancelledAmount' => 0,
                'overdueCount' => 0,
                'overdueAmount' => 0,
                'statusData' => ['labels' => [], 'data' => [], 'colors' => []],
                'categoryData' => ['labels' => [], 'data' => [], 'colors' => []],
                'monthlyData' => [],
                'branches' => collect(),
                'suppliers' => collect(),
                'categories' => collect(),
                'filters' => [],
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ];
        }
    }
}
