<?php

namespace App\Filament\Pages;

use App\Models\Invoice;
use App\Models\Branch;
use App\Models\Customer;
use App\Enums\InvoiceStatus;
use Filament\Pages\Page;
use Carbon\Carbon;

class InvoicesReportPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static string|\UnitEnum|null $navigationGroup = 'Relatorios';
    protected static ?int $navigationSort = 5;
    protected string $view = 'filament.pages.invoices-report-page';
    protected static ?string $title = 'Relatório de Faturas a Receber';
    protected static ?string $slug = 'relatorios/faturas';

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

                return $query;
            };

            // Get invoices with relationships
            $invoices = $baseQuery()
                ->with(['customer', 'contract', 'branch'])
                ->orderBy('due_date', 'desc')
                ->paginate(50);

            // Clone for aggregate calculations
            $query = $baseQuery();

            // Calculate KPIs
            $totalCount = $query->clone()->count();
            $totalAmount = $query->clone()->sum('total');

            $openAmount = $query->clone()->where('status', InvoiceStatus::OPEN->value)->sum('total');
            $openCount = $query->clone()->where('status', InvoiceStatus::OPEN->value)->count();

            $overdueAmount = $query->clone()->where('status', InvoiceStatus::OVERDUE->value)->sum('total');
            $overdueCount = $query->clone()->where('status', InvoiceStatus::OVERDUE->value)->count();

            $paidAmount = $query->clone()->where('status', InvoiceStatus::PAID->value)->sum('total');
            $paidCount = $query->clone()->where('status', InvoiceStatus::PAID->value)->count();

            $cancelledAmount = $query->clone()->where('status', InvoiceStatus::CANCELLED->value)->sum('total');
            $cancelledCount = $query->clone()->where('status', InvoiceStatus::CANCELLED->value)->count();

            // Prepare chart data
            $statusCount = [
                InvoiceStatus::OPEN->value => $openCount,
                InvoiceStatus::OVERDUE->value => $overdueCount,
                InvoiceStatus::PAID->value => $paidCount,
                InvoiceStatus::CANCELLED->value => $cancelledCount,
            ];

            $statusData = [
                'labels' => ['Abertas', 'Vencidas', 'Pagas', 'Canceladas'],
                'data' => [$openCount, $overdueCount, $paidCount, $cancelledCount],
                'colors' => ['#3b82f6', '#ef4444', '#10b981', '#6b7280']
            ];

            // Monthly trend data
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $count = Invoice::whereYear('due_date', $month->year)
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
                'invoices' => $invoices,
                'totalCount' => $totalCount,
                'totalAmount' => $totalAmount,
                'openCount' => $openCount,
                'openAmount' => $openAmount,
                'overdueCount' => $overdueCount,
                'overdueAmount' => $overdueAmount,
                'paidCount' => $paidCount,
                'paidAmount' => $paidAmount,
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
            \Log::error('InvoicesReportPage error: ' . $e->getMessage());

            return [
                'invoices' => collect(),
                'totalCount' => 0,
                'totalAmount' => 0,
                'openCount' => 0,
                'openAmount' => 0,
                'overdueCount' => 0,
                'overdueAmount' => 0,
                'paidCount' => 0,
                'paidAmount' => 0,
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
