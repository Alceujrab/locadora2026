<?php

namespace App\Filament\Resources\VehicleResource\Pages;

use App\Filament\Resources\VehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Infolists\Components;

class ViewVehicle extends ViewRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        $vehicle = $this->record;
        $vehicle->load(['contracts.customer', 'reservations.customer', 'serviceOrders', 'fines', 'inspections', 'maintenanceAlerts', 'category', 'branch', 'photos']);

        // Cálculos
        $totalContracts = $vehicle->contracts->count();
        $totalReservations = $vehicle->reservations->count();
        $totalServiceOrders = $vehicle->serviceOrders->count();
        $totalFines = $vehicle->fines->count();
        $totalInspections = $vehicle->inspections->count();

        $revenueContracts = $vehicle->contracts->sum('total');
        $revenueReservations = $vehicle->reservations->sum('total');
        $totalRevenue = $revenueContracts + $revenueReservations;

        $expensesOS = $vehicle->serviceOrders->sum('total');
        $expensesFines = $vehicle->fines->sum('amount');
        $expensesInsurance = (float) $vehicle->insurance_value;
        $totalExpenses = $expensesOS + $expensesFines + $expensesInsurance;

        $profit = $totalRevenue - $totalExpenses;
        $roi = $vehicle->purchase_value > 0 ? ($profit / (float) $vehicle->purchase_value) * 100 : 0;

        $activeContract = $vehicle->contracts->where('status.value', 'ativo')->first()
            ?? $vehicle->contracts->whereNull('actual_return_date')->first();

        $nextMaintenance = $vehicle->maintenanceAlerts->where('resolved_at', null)->sortBy('due_date')->first();

        $totalDaysRented = $vehicle->contracts->sum('total_days') + $vehicle->reservations->sum('total_days');
        $avgDailyRate = $totalDaysRented > 0 ? $totalRevenue / $totalDaysRented : 0;

        return $schema->schema([
            // KPIs
            Components\ViewEntry::make('kpis')
                ->view('filament.vehicle-dashboard', [
                    'vehicle' => $vehicle,
                    'totalContracts' => $totalContracts,
                    'totalReservations' => $totalReservations,
                    'totalServiceOrders' => $totalServiceOrders,
                    'totalFines' => $totalFines,
                    'totalInspections' => $totalInspections,
                    'totalRevenue' => $totalRevenue,
                    'totalExpenses' => $totalExpenses,
                    'profit' => $profit,
                    'roi' => $roi,
                    'expensesOS' => $expensesOS,
                    'expensesFines' => $expensesFines,
                    'expensesInsurance' => $expensesInsurance,
                    'revenueContracts' => $revenueContracts,
                    'revenueReservations' => $revenueReservations,
                    'activeContract' => $activeContract,
                    'nextMaintenance' => $nextMaintenance,
                    'totalDaysRented' => $totalDaysRented,
                    'avgDailyRate' => $avgDailyRate,
                ]),
        ]);
    }
}
