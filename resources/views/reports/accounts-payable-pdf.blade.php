<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Contas a Pagar</title>
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
            border-bottom: 2px solid #EF4444;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #EF4444;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            background-color: #fef2f2;
            border: 1px solid #EF4444;
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
            color: #EF4444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #EF4444;
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
        .status-pago {
            background-color: #dcfce7;
            color: #166534;
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
        .category-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .category-section h3 {
            color: #EF4444;
            border-bottom: 1px solid #EF4444;
            padding-bottom: 10px;
        }
        .category-total {
            text-align: right;
            font-weight: bold;
            color: #EF4444;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Relatório de Contas a Pagar</h1>
        <p>Período de {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
        <p>Data de Geração: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <label>Total de Despesas (R$)</label>
                <div class="value">{{ number_format($totals['total'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Despesas Pendentes (R$)</label>
                <div class="value">{{ number_format($totals['pending'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Despesas Vencidas (R$)</label>
                <div class="value">{{ number_format($totals['overdue'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Despesas Pagas (R$)</label>
                <div class="value">{{ number_format($totals['paid'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Despesas Canceladas (R$)</label>
                <div class="value">{{ number_format($totals['cancelled'], 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Taxa de Pagamento</label>
                <div class="value">{{ $totals['total'] > 0 ? number_format(($totals['paid'] / $totals['total']) * 100, 1) : 0 }}%</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Fornecedor</th>
                <th>Vencimento</th>
                <th>Categoria</th>
                <th class="text-right">Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                <tr>
                    <td>{{ $record->description }}</td>
                    <td>{{ $record->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $record->due_date->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($record->category) }}</td>
                    <td class="text-right">{{ number_format($record->amount, 2, ',', '.') }}</td>
                    <td>
                        <span class="status-{{ strtolower($record->status) }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #999;">Nenhuma conta a pagar encontrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!empty($byCategory))
        <div class="category-section">
            <h3>Resumo por Categoria</h3>
            @foreach($byCategory as $category => $amount)
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <span>{{ ucfirst($category) }}</span>
                    <span style="font-weight: bold;">R$ {{ number_format($amount, 2, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="category-total">
                Total: R$ {{ number_format(array_sum($byCategory), 2, ',', '.') }}
            </div>
        </div>
    @endif

    <div class="footer">
        <p>Este relatório foi gerado automaticamente pelo sistema Elite Locadora</p>
    </div>
</body>
</html>
