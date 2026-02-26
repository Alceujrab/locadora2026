<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Contas a Receber</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10B981;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #10B981;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f0fdf4;
            border: 1px solid #10B981;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
        }
        .summary-item label {
            font-weight: bold;
            color: #1e293b;
            display: block;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #10B981;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #10B981;
            color: white;
        }
        table th {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .status-pendente {
            background-color: #fef3c7;
            color: #92400e;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-parcial {
            background-color: #e9d5ff;
            color: #581c87;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-recebido {
            background-color: #dcfce7;
            color: #166534;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-inadimplente {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-cancelado {
            background-color: #f3f4f6;
            color: #374151;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Contas a Receber</h1>
        <p>Período de {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
        <p>Data de Geração: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <label>Total a Receber (R$)</label>
                <div class="value">{{ number_format($totals['total_amount'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Total Recebido (R$)</label>
                <div class="value">{{ number_format($totals['total_paid'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Saldo Pendente (R$)</label>
                <div class="value">{{ number_format($totals['total_remaining'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Contas Pendentes (R$)</label>
                <div class="value">{{ number_format($totals['pending'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Contas Parciais (R$)</label>
                <div class="value">{{ number_format($totals['partial'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Taxa de Arrecadação</label>
                <div class="value">{{ $totals['total_amount'] > 0 ? number_format(($totals['total_paid'] / $totals['total_amount']) * 100, 1) : 0 }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Cliente</th>
                <th>Vencimento</th>
                <th class="text-right">Valor Total</th>
                <th class="text-right">Valor Pago</th>
                <th class="text-right">Saldo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->description }}</td>
                    <td>{{ $record->customer->name ?? 'N/A' }}</td>
                    <td>{{ $record->due_date->format('d/m/Y') }}</td>
                    <td class="text-right">{{ number_format($record->amount, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($record->paid_amount, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($record->amount - $record->paid_amount, 2, ',', '.') }}</td>
                    <td>
                        <span class="status-{{ strtolower(str_replace(' ', '', $record->status)) }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">Nenhuma conta a receber encontrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema Elite Locadora</p>
    </div>
</body>
</html>
