<?php

namespace App\Filament\Pages;

use App\Models\AccountReceivable;
use App\Models\Branch;
use App\Models\Customer;
use Filament\Pages\Page;
use Carbon\Carbon;

class AccountsReceivableReportPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static string|\UnitEnum|null $navigationGroup = 'Relatorios';
    protected static ?int $navigationSort = 6;
    protected string $view = 'filament.pages.accounts-receivable-report-page';
    protected static ?string $title = 'Relatório de Contas a Receber';
    protected static ?string $slug = 'relatorios/contas-receber';

    public function getViewData(): array
    {
        try {
            // Get filters from request
            $dateFrom = request('date_from') ? Carbon::parse(request('date_from')) : now()->subDays(90);
            $dateTo = request('date_to') ? Carbon::parse(request('date_to')) : now()->addDays(30);
            $status = request('status');
            $customerId = request('customer_id');
            $branchId = request('branch_id');

            // Base query with date range
            $baseQuery = function() use ($dateFrom, $dateTo, $status, $customerId, $branchId) {
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

                return $query;
            };

            // Get records with relationships
            $records = $baseQuery()
                ->with(['customer', 'invoice', 'branch'])
                ->orderBy('due_date', 'desc')
                ->get();

            // Clone for aggregate calculations
            $query = $baseQuery();

            // Calculate KPIs
            $totalCount = $query->clone()->count();
            $totalAmount = $query->clone()->sum('amount');
            $totalPaidAmount = $query->clone()->sum('paid_amount');
            $totalRemaining = $totalAmount - $totalPaidAmount;

            $pendingAmount = $query->clone()->where('status', 'pendente')->sum('amount');
            $pendingCount = $query->clone()->where('status', 'pendente')->count();

            $partialAmount = $query->clone()->where('status', 'parcial')->sum('amount');
            $partialRemaining = $query->clone()->where('status', 'parcial')->sum(
                \DB::raw('amount - paid_amount')
            );
            $partialCount = $query->clone()->where('status', 'parcial')->count();

            $receivedAmount = $query->clone()->where('status', 'recebido')->sum('amount');
            $receivedCount = $query->clone()->where('status', 'recebido')->count();

            $delinquentAmount = $query->clone()->where('status', 'inadimplente')->sum('amount');
            $delinquentCount = $query->clone()->where('status', 'inadimplente')->count();

            $cancelledAmount = $query->clone()->where('status', 'cancelado')->sum('amount');
            $cancelledCount = $query->clone()->where('status', 'cancelado')->count();

            // Prepare chart data
            $statusData = [
                'labels' => ['Pendente', 'Parcial', 'Recebido', 'Inadimplente', 'Cancelado'],
                'data' => [$pendingCount, $partialCount, $receivedCount, $delinquentCount, $cancelledCount],
                'colors' => ['#3b82f6', '#f59e0b', '#10b981', '#ef4444', '#6b7280']
            ];

            // Monthly trend data
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = AccountReceivable::whereYear('due_date', $month->year)
                    ->whereMonth('due_date', $month->month)
                    ->count();
                $monthlyData[] = [
                    'month' => $month->format('M/y'),
                    'count' => $count
                ];
            }

            // Get branches and customers for filters
            $branches = Branch::all();
            $customers = Customer::all();

            return [
                'records' => $records,
                'totalCount' => $totalCount,
                'totalAmount' => $totalAmount,
                'totalPaidAmount' => $totalPaidAmount,
                'totalRemaining' => $totalRemaining,
                'pendingCount' => $pendingCount,
                'pendingAmount' => $pendingAmount,
                'partialCount' => $partialCount,
                'partialAmount' => $partialAmount,
                'partialRemaining' => $partialRemaining,
                'receivedCount' => $receivedCount,
                'receivedAmount' => $receivedAmount,
                'delinquentCount' => $delinquentCount,
                'delinquentAmount' => $delinquentAmount,
                'cancelledCount' => $cancelledCount,
                'cancelledAmount' => $cancelledAmount,
                'statusData' => $statusData,
                'monthlyData' => $monthlyData,
                'branches' => $branches,
                'customers' => $customers,
                'filters' => [
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to'),
                    'status' => $status,
                    'customer_id' => $customerId,
                    'branch_id' => $branchId,
                ]
            ];
        } catch (\Exception $e) {
            \Log::error('AccountsReceivableReportPage error: ' . $e->getMessage());

            return [
                'records' => collect(),
                'totalCount' => 0,
                'totalAmount' => 0,
                'totalPaidAmount' => 0,
                'totalRemaining' => 0,
                'pendingCount' => 0,
                'pendingAmount' => 0,
                'partialCount' => 0,
                'partialAmount' => 0,
                'partialRemaining' => 0,
                'receivedCount' => 0,
                'receivedAmount' => 0,
                'delinquentCount' => 0,
                'delinquentAmount' => 0,
                'cancelledCount' => 0,
                'cancelledAmount' => 0,
                'statusData' => ['labels' => [], 'data' => [], 'colors' => []],
                'monthlyData' => [],
                'branches' => collect(),
                'customers' => collect(),
                'filters' => [],
                'error' => 'Erro ao carregar dados: ' . $e->getMessage()
            ];
        }
    }
}
