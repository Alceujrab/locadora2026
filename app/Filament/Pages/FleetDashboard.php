<?php

namespace App\Filament\Pages;

use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use App\Models\MaintenanceAlert;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class FleetDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestao de Frota';

    protected static ?string $title = 'Painel da Frota';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.fleet-dashboard';

    public function getTitle(): string|Htmlable
    {
        return 'Visão Geral da Frota';
    }

    protected function getViewData(): array
    {
        try {
            $fleetCounts = [
                'total' => Vehicle::count(),
                'available' => Vehicle::where('status', VehicleStatus::AVAILABLE)->count(),
                'rented' => Vehicle::where('status', VehicleStatus::RENTED)->count(),
                'maintenance' => Vehicle::where('status', VehicleStatus::MAINTENANCE)->count(),
                'reserved' => Vehicle::where('status', VehicleStatus::RESERVED)->count(),
                'inactive' => Vehicle::where('status', VehicleStatus::INACTIVE)->count(),
            ];
        } catch (\Throwable $e) {
            $fleetCounts = ['total' => 0, 'available' => 0, 'rented' => 0, 'maintenance' => 0, 'reserved' => 0, 'inactive' => 0];
        }

        try {
            $alertDate = now()->addDays(30);
            $expiringDocs = Vehicle::where(function($q) use ($alertDate) {
                $q->whereBetween('ipva_due_date', [now(), $alertDate])
                  ->orWhereBetween('licensing_due_date', [now(), $alertDate])
                  ->orWhereBetween('insurance_expiry_date', [now(), $alertDate]);
            })->get();
        } catch (\Throwable $e) {
            $expiringDocs = collect();
        }

        try {
            $pendingMaintenances = MaintenanceAlert::with('vehicle')
                ->whereNull('resolved_at')
                ->orderBy('due_date', 'asc')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {
            $pendingMaintenances = collect();
        }

        // Dados adicionais para o dashboard
        try {
            $recentVehicles = Vehicle::with(['category', 'branch'])
                ->latest()
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {
            $recentVehicles = collect();
        }

        $utilizationRate = $fleetCounts['total'] > 0
            ? round(($fleetCounts['rented'] / $fleetCounts['total']) * 100, 1)
            : 0;

        return [
            'fleetCounts' => $fleetCounts,
            'expiringDocs' => $expiringDocs,
            'pendingMaintenances' => $pendingMaintenances,
            'recentVehicles' => $recentVehicles,
            'utilizationRate' => $utilizationRate,
            'error' => null,
        ];
    }
}
