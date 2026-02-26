<?php

namespace App\Http\Controllers;

use App\Models\AccountPayable;
use App\Models\AccountReceivable;
use Barryvdh\DomPDF\Facade\Pdf;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashFlowExportController extends Controller
{
    private function getData(Request $request): array
    {
        $dateFrom = $request->input('date_from') ? Carbon::parse($request->input('date_from')) : now()->startOfMonth();
        $dateTo = $request->input('date_to') ? Carbon::parse($request->input('date_to')) : now()->endOfMonth();
        $branchId = $request->input('branch_id');
        $type = $request->input('type');

        $inQuery = AccountReceivable::query()
            ->whereNotNull('received_at')
            ->whereIn('status', ['recebido', 'parcial'])
            ->whereBetween('received_at', [$dateFrom, $dateTo]);
        if ($branchId) $inQuery->where('branch_id', $branchId);
        $receivables = $inQuery->with(['customer', 'invoice'])->get();

        $outQuery = AccountPayable::query()
            ->whereNotNull('paid_at')
            ->where('status', 'pago')
            ->whereBetween('paid_at', [$dateFrom, $dateTo]);
        if ($branchId) $outQuery->where('branch_id', $branchId);
        $payables = $outQuery->with(['supplier', 'vehicle'])->get();

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

        $transactions = $transactions->sortBy('date')->values();

        $balance = 0;
        $transactionsWithBalance = [];
        foreach ($transactions as $t) {
            $balance += ($t['type'] === 'entrada') ? $t['amount'] : -$t['amount'];
            $t['balance'] = $balance;
            $transactionsWithBalance[] = $t;
        }

        $totalIn = $transactions->where('type', 'entrada')->sum('amount');
        $totalOut = $transactions->where('type', 'saida')->sum('amount');
        $netFlow = $totalIn - $totalOut;

        return [
            'transactions' => $transactionsWithBalance,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'netFlow' => $netFlow,
            'transactionCount' => $transactions->count(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    public function exportPdf(Request $request)
    {
        try {
            $data = $this->getData($request);

            $pdf = Pdf::loadView('reports.cash-flow-pdf', $data)
                ->setPaper('a4', 'landscape')
                ->setOption('isPhpEnabled', true)
                ->setOption('isHtml5ParserEnabled', true);

            return $pdf->download('Fluxo-de-Caixa-' . now()->format('d-m-Y') . '.pdf');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar PDF: ' . $e->getMessage()], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $data = $this->getData($request);

            $fileName = 'Fluxo-de-Caixa-' . now()->format('d-m-Y') . '.xlsx';
            $filePath = storage_path('app/' . $fileName);

            $options = new Options();
            $writer = new Writer($options);
            $writer->openToFile($filePath);

            $headerStyle = (new Style())->setFontBold()->setFontColor(Color::WHITE)->setBackgroundColor('3B82F6');
            $writer->addRow(Row::fromValues([
                'Data', 'Tipo', 'Descricao', 'Entidade', 'Valor (R$)', 'Saldo (R$)',
            ], $headerStyle));

            foreach ($data['transactions'] as $t) {
                $writer->addRow(Row::fromValues([
                    $t['date']->format('d/m/Y'),
                    $t['type'] === 'entrada' ? 'Entrada' : 'Saida',
                    $t['description'],
                    $t['entity'],
                    ($t['type'] === 'entrada' ? '+' : '-') . number_format($t['amount'], 2, ',', '.'),
                    number_format($t['balance'], 2, ',', '.'),
                ]));
            }

            $totalStyle = (new Style())->setFontBold();
            $writer->addRow(Row::fromValues([
                '', '', '', 'TOTAL ENTRADAS', number_format($data['totalIn'], 2, ',', '.'), '',
            ], $totalStyle));
            $writer->addRow(Row::fromValues([
                '', '', '', 'TOTAL SAIDAS', number_format($data['totalOut'], 2, ',', '.'), '',
            ], $totalStyle));
            $writer->addRow(Row::fromValues([
                '', '', '', 'SALDO', number_format($data['netFlow'], 2, ',', '.'), '',
            ], $totalStyle));

            $writer->close();

            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao gerar Excel: ' . $e->getMessage()], 500);
        }
    }
}
