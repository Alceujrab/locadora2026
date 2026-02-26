<?php

namespace App\Filament\Pages;

use App\Models\Vehicle;
use App\Models\Contract;
use App\Models\AccountPayable;
use App\Models\Branch;
use App\Enums\ContractStatus;
use Filament\Pages\Page;
use Carbon\Carbon;

class FleetProfitabilityPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static string|\UnitEnum|null $navigationGroup = 'Relatorios';
    protected static ?int $navigationSort = 8;
    protected string $view = 'filament.pages.fleet-profitability-page';
    protected static ?string $title = 'Lucratividade da Frota';
    protected static ?string $slug = 'relatorios/lucratividade-frota';

    public function getViewData(): array
    {
        try {
            $dateFrom = request('date_from') ? Carbon::parse(request('date_from')) : now()->startOfYear();
            $dateTo = request('date_to') ? Carbon::parse(request('date_to')) : now();
            $branchId = request('branch_id');
            $vehicleId = request('vehicle_id');

            $vehiclesQuery = Vehicle::query()->whereNull('deleted_at');
            if ($branchId) {
                $vehiclesQuery->where('branch_id', $branchId);
            }
            if ($vehicleId) {
                $vehiclesQuery->where('id', $vehicleId);
            }

            $vehicles = $vehiclesQuery->with(['branch', 'category'])->get();
            $vehicleIds = $vehicles->pluck('id')->toArray();

            // Revenue: Contracts (ativo + finalizado) per vehicle
            $revenueByVehicle = Contract::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', [ContractStatus::ACTIVE->value, ContractStatus::FINISHED->value])
                ->where(function ($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('pickup_date', [$dateFrom, $dateTo])
                      ->orWhereBetween('return_date', [$dateFrom, $dateTo]);
                })
                ->selectRaw('vehicle_id, SUM(total) as total_revenue, COUNT(*) as contract_count')
                ->groupBy('vehicle_id')
                ->get()
                ->keyBy('vehicle_id');

            // Expenses: Accounts Payable per vehicle
            $expensesByVehicle = AccountPayable::whereIn('vehicle_id', $vehicleIds)
                ->whereBetween('due_date', [$dateFrom, $dateTo])
                ->where('status', '!=', 'cancelado')
                ->selectRaw('vehicle_id, SUM(amount) as total_expense, COUNT(*) as expense_count')
                ->groupBy('vehicle_id')
                ->get()
                ->keyBy('vehicle_id');

            $vehicleData = [];
            $totalRevenue = 0;
            $totalExpenses = 0;

            foreach ($vehicles as $vehicle) {
                $revenue = (float) ($revenueByVehicle[$vehicle->id]->total_revenue ?? 0);
                $expenses = (float) ($expensesByVehicle[$vehicle->id]->total_expense ?? 0);
                $profit = $revenue - $expenses;
                $margin = $revenue > 0 ? ($profit / $revenue) * 100 : ($expenses > 0 ? -100 : 0);

                $totalRevenue += $revenue;
                $totalExpenses += $expenses;

                $vehicleData[] = [
                    'id' => $vehicle->id,
                    'plate' => $vehicle->plate,
                    'model' => $vehicle->brand . ' ' . $vehicle->model,
                    'year' => $vehicle->year_model,
                    'branch' => $vehicle->branch->name ?? 'N/A',
                    'category' => $vehicle->category->name ?? 'N/A',
                    'status' => $vehicle->status,
                    'contracts' => $revenueByVehicle[$vehicle->id]->contract_count ?? 0,
                    'revenue' => $revenue,
                    'expenses' => $expenses,
                    'profit' => $profit,
                    'margin' => $margin,
                ];
            }

            usort($vehicleData, fn ($a, $b) => $b['profit'] <=> $a['profit']);

            $totalProfit = $totalRevenue - $totalExpenses;
            $totalMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;
            $activeVehicles = $vehicles->count();
            $revenuePerVehicle = $activeVehicles > 0 ? $totalRevenue / $activeVehicles : 0;

            // Chart: Top 10 by profit
            $top10 = array_slice($vehicleData, 0, 10);
            $profitChartData = [
                'labels' => array_map(fn ($v) => $v['plate'], $top10),
                'revenue' => array_map(fn ($v) => $v['revenue'], $top10),
                'expenses' => array_map(fn ($v) => $v['expenses'], $top10),
                'profit' => array_map(fn ($v) => $v['profit'], $top10),
            ];

            $distributionData = [
                'labels' => ['Receitas', 'Despesas'],
                'data' => [$totalRevenue, $totalExpenses],
                'colors' => ['#10b981', '#ef4444'],
            ];

            // Monthly trend (last 6 months)
            $monthlyData = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $mStart = $month->copy()->startOfMonth();
                $mEnd = $month->copy()->endOfMonth();

                $mRevenue = Contract::whereIn('vehicle_id', $vehicleIds)
                    ->whereIn('status', [ContractStatus::ACTIVE->value, ContractStatus::FINISHED->value])
                    ->whereBetween('pickup_date', [$mStart, $mEnd])
                    ->sum('total');

                $mExpenses = AccountPayable::whereIn('vehicle_id', $vehicleIds)
                    ->whereBetween('due_date', [$mStart, $mEnd])
                    ->where('status', '!=', 'cancelado')
                    ->sum('amount');

                $monthlyData[] = [
                    'month' => $month->format('M/y'),
                    'revenue' => (float) $mRevenue,
                    'expenses' => (float) $mExpenses,
                    'profit' => (float) $mRevenue - (float) $mExpenses,
                ];
            }

            $branches = Branch::all();
            $allVehicles = Vehicle::whereNull('deleted_at')->get();

            return [
                'vehicleData' => $vehicleData,
                'totalRevenue' => $totalRevenue,
                'totalExpenses' => $totalExpenses,
                'totalProfit' => $totalProfit,
                'totalMargin' => $totalMargin,
                'activeVehicles' => $activeVehicles,
                'revenuePerVehicle' => $revenuePerVehicle,
                'profitChartData' => $profitChartData,
                'distributionData' => $distributionData,
                'monthlyData' => $monthlyData,
                'branches' => $branches,
                'allVehicles' => $allVehicles,
                'filters' => [
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to'),
                    'branch_id' => $branchId,
                    'vehicle_id' => $vehicleId,
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('FleetProfitabilityPage error: ' . $e->getMessage());

            return [
                'vehicleData' => [],
                'totalRevenue' => 0,
                'totalExpenses' => 0,
                'totalProfit' => 0,
                'totalMargin' => 0,
                'activeVehicles' => 0,
                'revenuePerVehicle' => 0,
                'profitChartData' => ['labels' => [], 'revenue' => [], 'expenses' => [], 'profit' => []],
                'distributionData' => ['labels' => [], 'data' => [], 'colors' => []],
                'monthlyData' => [],
                'branches' => collect(),
                'allVehicles' => collect(),
                'filters' => [],
                'error' => 'Erro ao carregar dados: ' . $e->getMessage(),
            ];
        }
    }
}
