<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Contract;
use App\Models\Invoice;

class InvoiceService
{
    /**
     * Gera faturas (parcelas) a partir de um Contrato
     */
    public function generateForContract(Contract $contract, int $installments = 1, int $dueDays = 5): array
    {
        if ($installments < 1) {
            $installments = 1;
        }

        $totalAmount = $contract->total;
        $installmentAmount = round($totalAmount / $installments, 2);

        // Ajustar a última parcela para corrigir dízimas
        $totalCalculated = $installmentAmount * $installments;
        $difference = $totalAmount - $totalCalculated;

        $invoices = [];
        $baseDate = now()->addDays($dueDays);

        for ($i = 1; $i <= $installments; $i++) {
            $amount = $installmentAmount;

            // Adiciona a diferença do arredondamento na última parcela
            if ($i === $installments && $difference != 0) {
                $amount += $difference;
            }

            $invoice = Invoice::create([
                'branch_id' => $contract->branch_id,
                'contract_id' => $contract->id,
                'customer_id' => $contract->customer_id,
                'invoice_number' => $this->generateInvoiceNumber($contract->branch_id),
                'due_date' => $baseDate->copy()->addMonths($i - 1),
                'installment_number' => $i,
                'total_installments' => $installments,
                'amount' => $amount,
                'total' => $amount,
                'status' => InvoiceStatus::OPEN,
            ]);

            $invoices[] = $invoice;
        }

        return $invoices;
    }

    /**
     * Gera uma fatura avulsa
     */
    public function createCustomInvoice(array $data): Invoice
    {
        $data['invoice_number'] = $this->generateInvoiceNumber($data['branch_id'] ?? null);
        $data['status'] = $data['status'] ?? InvoiceStatus::OPEN;
        $data['total'] = $data['amount'] - ($data['discount'] ?? 0);

        return Invoice::create($data);
    }

    /**
     * Helper para gerar número único sequencial da fatura
     */
    private function generateInvoiceNumber(?string $branchId = null): string
    {
        $prefix = 'INV'.date('ym');
        $count = Invoice::whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count() + 1;

        return sprintf('%s%04d', $prefix, $count);
    }
}
