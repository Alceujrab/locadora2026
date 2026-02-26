<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Contract;
use App\Models\AccountPayable;
use App\Enums\ContractStatus;
use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FleetProfitabilityExportController extends Controller
{
    private function getData(Request $request): array
    {
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfYear();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now();
        $branchId = $request->input('branch_id');
        $vehicleId = $request->input('vehicle_id');

        $vehiclesQuery = Vehicle::query()->whereNull('deleted_at');
        if ($branchId) $vehiclesQuery->where('branch_id', $branchId);
        if ($vehicleId) $vehiclesQuery->where('id', $vehicleId);

        $vehicles = $vehiclesQuery->with(['branch', 'category'])->get();
        $vehicleIds = $vehicles->pluck('id')->toArray();

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
                'plate' => $vehicle->plate,
                'model' => $vehicle->brand . ' ' . $vehicle->model,
                'branch' => $vehicle->branch->name ?? 'N/A',
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

        return [
            'vehicleData' => $vehicleData,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'totalMargin' => $totalMargin,
            'activeVehicles' => count($vehicleData),
            'revenuePerVehicle' => count($vehicleData) > 0 ? $totalRevenue / count($vehicleData) : 0,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    public function exportPdf(Request $request)
    {
        try {
            $data = $this->getData($request);

            $pdf = Pdf::loadView('reports.fleet-profitability-pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOption('isPhpEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);

            return $pdf->download('Lucratividade-Frota-' . now()->format('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $data = $this->getData($request);

            $fileName = 'Lucratividade-Frota-' . now()->format('d-m-Y') . '.xlsx';
            $filePath = storage_path('app/' . $fileName);

            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile($filePath);

            $headerStyle = (new Style())->setFontBold()->setFontColor(Color::WHITE)->setBackgroundColor('3B82F6');
            $writer->addRow(Row::fromValues([
                'Placa', 'Modelo', 'Filial', 'Contratos',
                'Receita (R$)', 'Despesas (R$)', 'Lucro (R$)', 'Margem (%)',
            ], $headerStyle));

            foreach ($data['vehicleData'] as $v) {
                $writer->addRow(Row::fromValues([
                    $v['plate'],
                    $v['model'],
                    $v['branch'],
                    $v['contracts'],
                    number_format($v['revenue'], 2, ',', '.'),
                    number_format($v['expenses'], 2, ',', '.'),
                    number_format($v['profit'], 2, ',', '.'),
                    number_format($v['margin'], 1, ',', '.') . '%',
                ]));
            }

            // Totals row
            $totalStyle = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                'TOTAL', '', '', '',
                number_format($data['totalRevenue'], 2, ',', '.'),
                number_format($data['totalExpenses'], 2, ',', '.'),
                number_format($data['totalProfit'], 2, ',', '.'),
                number_format($data['totalMargin'], 1, ',', '.') . '%',
            ], $totalStyle));

            $writer->close();

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
