<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class VehicleReportController extends Controller
{
    public function download(int $id)
    {
        $vehicle = Vehicle::with([
            'contracts.customer',
            'reservations.customer',
            'serviceOrders',
            'fines',
            'inspections',
            'maintenanceAlerts',
            'category',
            'branch',
        ])->findOrFail($id);

        // Cálculos
        $totalContracts = $vehicle->contracts->count();
        $totalReservations = $vehicle->reservations->count();
        $totalLocations = $totalContracts + $totalReservations;
        $totalServiceOrders = $vehicle->serviceOrders->count();
        $totalFines = $vehicle->fines->count();

        $revenueContracts = $vehicle->contracts->sum('total');
        $revenueReservations = $vehicle->reservations->sum('total');
        $totalRevenue = $revenueContracts + $revenueReservations;

        $expensesOS = $vehicle->serviceOrders->sum('total');
        $expensesFines = $vehicle->fines->sum('amount');
        $expensesInsurance = (float) $vehicle->insurance_value;
        $totalExpenses = $expensesOS + $expensesFines + $expensesInsurance;

        $profit = $totalRevenue - $totalExpenses;
        $totalDaysRented = $vehicle->contracts->sum('total_days') + $vehicle->reservations->sum('total_days');
        $avgDailyRate = $totalDaysRented > 0 ? $totalRevenue / $totalDaysRented : 0;

        // Logo
        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Empresa
        $company = [
            'name'    => Setting::get('company_name',    'Elite Locadora de Veiculos'),
            'cnpj'    => Setting::get('company_cnpj',    ''),
            'phone'   => Setting::get('company_phone',   ''),
            'email'   => Setting::get('company_email',   ''),
            'address' => Setting::get('company_address', ''),
            'city'    => Setting::get('company_city',    ''),
            'state'   => Setting::get('company_state',   ''),
            'footer'  => Setting::get('invoice_footer',  'Este documento não possui validade fiscal.'),
        ];

        $pdf = Pdf::loadView('pdf.vehicle-report', [
            'vehicle' => $vehicle,
            'totalContracts' => $totalContracts,
            'totalReservations' => $totalReservations,
            'totalLocations' => $totalLocations,
            'totalServiceOrders' => $totalServiceOrders,
            'totalFines' => $totalFines,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'revenueContracts' => $revenueContracts,
            'revenueReservations' => $revenueReservations,
            'expensesOS' => $expensesOS,
            'expensesFines' => $expensesFines,
            'expensesInsurance' => $expensesInsurance,
            'profit' => $profit,
            'totalDaysRented' => $totalDaysRented,
            'avgDailyRate' => $avgDailyRate,
            'logoBase64' => $logoBase64,
            'company' => $company,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Relatorio-Veiculo-' . $vehicle->plate . '.pdf');
    }
}
