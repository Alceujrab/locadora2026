<?php

namespace App\Filament\Pages;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\VehicleStatus;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\MaintenanceAlert;
use App\Models\Reservation;
use App\Models\Vehicle;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class FleetDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestão de Frota';

    protected static ?string $title = 'Painel Executivo';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.fleet-dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Painel Executivo';
    }

    protected function getViewData(): array
    {
        // ---------- FROTA ----------
        try {
            $fleetCounts = [
                'total'       => Vehicle::count(),
                'available'   => Vehicle::where('status', VehicleStatus::AVAILABLE)->count(),
                'rented'      => Vehicle::where('status', VehicleStatus::RENTED)->count(),
                'reserved'    => Vehicle::where('status', VehicleStatus::RESERVED)->count(),
                'maintenance' => Vehicle::where('status', VehicleStatus::MAINTENANCE)->count(),
                'inactive'    => Vehicle::where('status', VehicleStatus::INACTIVE)->count(),
            ];
        } catch (\Throwable $e) {
            $fleetCounts = ['total' => 0, 'available' => 0, 'rented' => 0, 'reserved' => 0, 'maintenance' => 0, 'inactive' => 0];
        }

        $utilizationRate = $fleetCounts['total'] > 0
            ? round(($fleetCounts['rented'] / $fleetCounts['total']) * 100, 1)
            : 0;

        // ---------- DOCUMENTOS VENCENDO ----------
        try {
            $alertDate = now()->addDays(30);
            $expiringDocs = Vehicle::where(function ($q) use ($alertDate) {
                $q->whereBetween('ipva_due_date', [now()->subDays(30), $alertDate])
                    ->orWhereBetween('licensing_due_date', [now()->subDays(30), $alertDate])
                    ->orWhereBetween('insurance_expiry_date', [now()->subDays(30), $alertDate]);
            })->limit(20)->get();
        } catch (\Throwable $e) {
            $expiringDocs = collect();
        }

        // ---------- MANUTENÇÕES PENDENTES ----------
        try {
            $pendingMaintenances = MaintenanceAlert::with('vehicle')
                ->whereNull('resolved_at')
                ->orderBy('due_date', 'asc')
                ->limit(8)
                ->get();
        } catch (\Throwable $e) {
            $pendingMaintenances = collect();
        }

        // ---------- VEÍCULOS RECENTES ----------
        try {
            $recentVehicles = Vehicle::with(['category', 'branch'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $recentVehicles = collect();
        }

        // ---------- FINANCEIRO ----------
        $financial = [
            'mtd_revenue'      => 0.0,
            'ytd_revenue'      => 0.0,
            'open_amount'      => 0.0,
            'overdue_amount'   => 0.0,
            'overdue_count'    => 0,
            'active_contracts' => 0,
            'pending_reserv'   => 0,
            'total_customers'  => 0,
        ];

        try {
            $financial['mtd_revenue'] = (float) Invoice::where('status', InvoiceStatus::PAID)
                ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total');

            $financial['ytd_revenue'] = (float) Invoice::where('status', InvoiceStatus::PAID)
                ->whereBetween('paid_at', [now()->startOfYear(), now()->endOfYear()])
                ->sum('total');

            $financial['open_amount']    = (float) Invoice::where('status', InvoiceStatus::OPEN)->sum('total');
            $financial['overdue_amount'] = (float) Invoice::where('status', InvoiceStatus::OVERDUE)->sum('total');
            $financial['overdue_count']  = (int) Invoice::where('status', InvoiceStatus::OVERDUE)->count();
        } catch (\Throwable $e) {
        }

        try {
            $financial['active_contracts'] = (int) Contract::where('status', ContractStatus::ACTIVE)->count();
        } catch (\Throwable $e) {
        }

        try {
            $financial['pending_reserv'] = (int) Reservation::whereIn('status', ['pendente', 'confirmada'])->count();
        } catch (\Throwable $e) {
        }

        try {
            $financial['total_customers'] = (int) Customer::count();
        } catch (\Throwable $e) {
        }

        // ---------- TENDÊNCIA DE RECEITA (6 MESES) ----------
        $revenueTrend = ['labels' => [], 'values' => []];

        try {
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->copy()->subMonths($i);
                $label = ucfirst($month->translatedFormat('M/y'));
                $value = (float) Invoice::where('status', InvoiceStatus::PAID)
                    ->whereBetween('paid_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                    ->sum('total');

                $revenueTrend['labels'][] = $label;
                $revenueTrend['values'][] = round($value, 2);
            }
        } catch (\Throwable $e) {
            $revenueTrend = ['labels' => [], 'values' => []];
        }

        // ---------- CONTRATOS RECENTES ----------
        try {
            $recentContracts = Contract::with(['customer', 'vehicle'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $recentContracts = collect();
        }

        return [
            'fleetCounts'         => $fleetCounts,
            'utilizationRate'     => $utilizationRate,
            'expiringDocs'        => $expiringDocs,
            'pendingMaintenances' => $pendingMaintenances,
            'recentVehicles'      => $recentVehicles,
            'recentContracts'     => $recentContracts,
            'financial'           => $financial,
            'revenueTrend'        => $revenueTrend,
            'generatedAt'         => now(),
        ];
    }
}
