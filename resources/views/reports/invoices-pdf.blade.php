<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Faturas a Receber</title>
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
            border-bottom: 2px solid #3B82F6;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #3B82F6;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #f0f9ff;
            border: 1px solid #3B82F6;
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
            color: #3B82F6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #3B82F6;
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
        .status-aberta {
            background-color: #dbeafe;
            color: #1e40af;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-vencida {
            background-color: #fee2e2;
            color: #991b1b;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-paga {
            background-color: #dcfce7;
            color: #166534;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
        }
        .status-cancelada {
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
        <h1>Relatório de Faturas a Receber</h1>
        <p>Período de {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
        <p>Data de Geração: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <label>Total Geral (R$)</label>
                <div class="value">{{ number_format($totals['total'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Faturas Abertas (R$)</label>
                <div class="value">{{ number_format($totals['open'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Faturas Vencidas (R$)</label>
                <div class="value">{{ number_format($totals['overdue'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Faturas Pagas (R$)</label>
                <div class="value">{{ number_format($totals['paid'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Faturas Canceladas (R$)</label>
                <div class="value">{{ number_format($totals['cancelled'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Taxa de Recebimento</label>
                <div class="value">{{ $totals['total'] > 0 ? number_format(($totals['paid'] / $totals['total']) * 100, 1) : 0 }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Contrato</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td>{{ $invoice->contract->number ?? 'N/A' }}</td>
                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="status-{{ strtolower($invoice->status) }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                    </td>
                    <td class="text-right">{{ number_format($invoice->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Nenhuma fatura encontrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema Elite Locadora</p>
    </div>
</body>
</html>
