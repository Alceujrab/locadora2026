<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsReceivableExport implements FromArray, WithHeadings, WithStyles
{
    private $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function headings(): array
    {
        return [
            'Descrição',
            'Cliente',
            'Fatura',
            'Data de Vencimento',
            'Valor Total',
            'Valor Recebido',
            'Saldo',
            'Status',
            'Método de Pagamento',
            'Data de Recebimento',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->records as $record) {
            $data[] = [
                $record->description,
                $record->customer->name ?? 'N/A',
                $record->invoice->invoice_number ?? '-',
                $record->due_date->format('d/m/Y'),
                number_format($record->amount, 2, ',', '.'),
                number_format($record->paid_amount, 2, ',', '.'),
                number_format($record->amount - $record->paid_amount, 2, ',', '.'),
                ucfirst($record->status),
                $record->payment_method ?? '-',
                $record->received_at ? $record->received_at->format('d/m/Y') : '-',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '10B981']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
