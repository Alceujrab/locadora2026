<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AccountsPayableExport implements FromArray, WithHeadings, WithStyles
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
            'Fornecedor',
            'Data de Vencimento',
            'Categoria',
            'Valor',
            'Status',
            'Método de Pagamento',
            'Veículo',
            'Data de Pagamento',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->records as $record) {
            $data[] = [
                $record->description,
                $record->supplier->name ?? 'N/A',
                $record->due_date->format('d/m/Y'),
                ucfirst($record->category),
                number_format($record->amount, 2, ',', '.'),
                ucfirst($record->status),
                $record->payment_method ?? '-',
                $record->vehicle->plate ?? '-',
                $record->paid_at ? $record->paid_at->format('d/m/Y') : '-',
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'EF4444']],
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
            ],
        ];
    }
}
