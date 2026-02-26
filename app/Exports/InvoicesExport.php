<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromArray, WithHeadings, WithStyles
{
    private $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function headings(): array
    {
        return [
            'Número da Fatura',
            'Cliente',
            'Contrato',
            'Data de Vencimento',
            'Status',
            'Valor Total',
            'Data de Pagamento',
            'Método de Pagamento',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->invoices as $invoice) {
            $data[] = [
                $invoice->invoice_number,
                $invoice->customer->name ?? 'N/A',
                $invoice->contract->number ?? 'N/A',
                $invoice->due_date->format('d/m/Y'),
                ucfirst($invoice->status),
                number_format($invoice->total, 2, ',', '.'),
                $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : '-',
                $invoice->payment_method ?? '-',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '3B82F6']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
