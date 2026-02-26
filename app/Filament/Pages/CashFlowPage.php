<?php

namespace App\Filament\Pages;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use App\Models\Branch;
use Filament\Pages\Page;
use Carbon\Carbon;

class CashFlowPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static string|\UnitEnum|null $navigationGroup = 'Relatorios';
    protected static ?int $navigationSort = 9;
    protected string $view = 'filament.pages.cash-flow-page';
    protected static ?string $title = 'Fluxo de Caixa';
    protected static ?string $slug = 'relatorios/fluxo-de-caixa';

    public function getViewData(): array
    {
        try {
            $dateFrom = request('date_from') ? Carbon::parse(request('date_from')) : now()->startOfMonth();
            $dateTo = request('date_to') ? Carbon::parse(request('date_to')) : now()->endOfMonth();
            $branchId = request('branch_id');
            $type = request('type'); // entrada, saida, or empty for all

            // ===== ENTRADAS (Recebíveis pagos) =====
            $inQuery = AccountReceivable::query()
                ->whereNotNull('received_at')
                ->whereIn('status', ['recebido', 'parcial'])
                ->whereBetween('received_at', [$dateFrom, $dateTo]);
            if ($branchId) $inQuery->where('branch_id', $branchId);

            $receivables = $inQuery->with(['customer', 'invoice'])->get();

            // ===== SAÍDAS (Pagamentos feitos) =====
            $outQuery = AccountPayable::query()
                ->whereNotNull('paid_at')
                ->where('status', 'pago')
                ->whereBetween('paid_at', [$dateFrom, $dateTo]);
            if ($branchId) $outQuery->where('branch_id', $branchId);

            $payables = $outQuery->with(['supplier', 'vehicle'])->get();

            // Build unified transactions list
            $transactions = collect();

            if (!$type || $type === 'entrada') {
                foreach ($receivables as $r) {
                    $transactions->push([
                        'date' => $r->received_at,
                        'type' => 'entrada',
                        'description' => $r->description ?: ('Fatura ' . ($r->invoice->invoice_number ?? 'N/A')),
                        'entity' => $r->customer->name ?? 'N/A',
                        'amount' => (float) ($r->paid_amount ?: $r->amount),
                    ]);
                }
            }

            if (!$type || $type === 'saida') {
                foreach ($payables as $p) {
                    $transactions->push([
                        'date' => $p->paid_at,
                        'type' => 'saida',
                        'description' => $p->description ?: ($p->category ?? 'Despesa'),
                        'entity' => $p->supplier->name ?? ($p->vehicle->plate ?? 'N/A'),
                        'amount' => (float) $p->amount,
                    ]);
                }
            }

            // Sort by date
            $transactions = $transactions->sortBy('date')->values();

            // Calculate running balance
            $balance = 0;
            $transactionsWithBalance = [];
            foreach ($transactions as $t) {
                if ($t['type'] === 'entrada') {
                    $balance += $t['amount'];
                } else {
                    $balance -= $t['amount'];
                }
                $t['balance'] = $balance;
                $transactionsWithBalance[] = $t;
            }

            // KPIs
            $totalIn = $transactions->where('type', 'entrada')->sum('amount');
            $totalOut = $transactions->where('type', 'saida')->sum('amount');
            $netFlow = $totalIn - $totalOut;
            $transactionCount = $transactions->count();

            // Projected: pending receivables - pending payables in the period
            $projectedIn = AccountReceivable::where('status', 'pendente')
                ->whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($branchId) $projectedIn->where('branch_id', $branchId);
            $projectedIn = (float) $projectedIn->sum('amount');

            $projectedOut = AccountPayable::where('status', 'pendente')
                ->whereBetween('due_date', [$dateFrom, $dateTo]);
            if ($branchId) $projectedOut->where('branch_id', $branchId);
            $projectedOut = (float) $projectedOut->sum('amount');

            $projectedBalance = $netFlow + $projectedIn - $projectedOut;

            // Chart: Daily flow
            $dailyData = [];
            $period = Carbon::parse($dateFrom)->toPeriod($dateTo);
            foreach ($period as $day) {
                $dayStr = $day->format('Y-m-d');
                $dayIn = $transactions->filter(fn ($t) => $t['type'] === 'entrada' && $t['date']->format('Y-m-d') === $dayStr)->sum('amount');
                $dayOut = $transactions->filter(fn ($t) => $t['type'] === 'saida' && $t['date']->format('Y-m-d') === $dayStr)->sum('amount');
                $dailyData[] = [
                    'day' => $day->format('d/m'),
                    'in' => $dayIn,
                    'out' => $dayOut,
                    'net' => $dayIn - $dayOut,
                ];
            }

            // Chart: Distribution
            $distributionData = [
                'labels' => ['Entradas', 'Saidas'],
                'data' => [$totalIn, $totalOut],
                'colors' => ['#10b981', '#ef4444'],
            ];

            $branches = Branch::all();

            return [
                'transactions' => $transactionsWithBalance,
                'totalIn' => $totalIn,
                'totalOut' => $totalOut,
                'netFlow' => $netFlow,
                'transactionCount' => $transactionCount,
                'projectedIn' => $projectedIn,
                'projectedOut' => $projectedOut,
                'projectedBalance' => $projectedBalance,
                'dailyData' => $dailyData,
                'distributionData' => $distributionData,
                'branches' => $branches,
                'filters' => [
                    'date_from' => request('date_from'),
                    'date_to' => request('date_to'),
                    'branch_id' => $branchId,
                    'type' => $type,
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('CashFlowPage error: ' . $e->getMessage());

            return [
                'transactions' => [],
                'totalIn' => 0,
                'totalOut' => 0,
                'netFlow' => 0,
                'transactionCount' => 0,
                'projectedIn' => 0,
                'projectedOut' => 0,
                'projectedBalance' => 0,
                'dailyData' => [],
                'distributionData' => ['labels' => [], 'data' => [], 'colors' => []],
                'branches' => collect(),
                'filters' => [],
                'error' => 'Erro ao carregar dados: ' . $e->getMessage(),
            ];
        }
    }
}
