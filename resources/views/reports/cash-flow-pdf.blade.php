<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fluxo de Caixa</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3B82F6; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #3B82F6; }
        .header p { margin: 5px 0; color: #666; }
        .summary { background-color: #f0f9ff; border: 1px solid #3B82F6; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .summary-item { text-align: center; }
        .summary-item label { font-weight: bold; color: #1e293b; display: block; margin-bottom: 5px; }
        .summary-item .value { font-size: 18px; font-weight: bold; color: #3B82F6; }
        .summary-item .value.green { color: #16a34a; }
        .summary-item .value.red { color: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 11px; }
        table thead { background-color: #3B82F6; color: white; }
        table th { border: 1px solid #ddd; padding: 8px; text-align: left; font-weight: bold; }
        table td { border: 1px solid #ddd; padding: 8px; }
        table tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; padding-top: 15px; }
        .positive { color: #16a34a; }
        .negative { color: #dc2626; }
        .badge { padding: 3px 8px; border-radius: 4px; font-weight: bold; font-size: 10px; }
        .badge-entrada { background-color: #dcfce7; color: #166534; }
        .badge-saida { background-color: #fee2e2; color: #991b1b; }
        .total-row { background-color: #e0f2fe !important; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Fluxo de Caixa</h1>
        <p>Periodo de {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
        <p>Data de Geracao: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <label>Total Entradas (R$)</label>
                <div class="value green">{{ number_format($totalIn, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Total Saidas (R$)</label>
                <div class="value red">{{ number_format($totalOut, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Saldo (R$)</label>
                <div class="value {{ $netFlow >= 0 ? 'green' : 'red' }}">{{ number_format($netFlow, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Transacoes</label>
                <div class="value">{{ $transactionCount }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Descricao</th>
                <th>Entidade</th>
                <th class="text-right">Valor (R$)</th>
                <th class="text-right">Saldo (R$)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                @php $isIn = $t['type'] === 'entrada'; @endphp
                <tr>
                    <td>{{ $t['date']->format('d/m/Y') }}</td>
                    <td><span class="badge {{ $isIn ? 'badge-entrada' : 'badge-saida' }}">{{ $isIn ? 'Entrada' : 'Saida' }}</span></td>
                    <td>{{ \Illuminate\Support\Str::limit($t['description'], 45) }}</td>
                    <td>{{ $t['entity'] }}</td>
                    <td class="text-right {{ $isIn ? 'positive' : 'negative' }}">{{ $isIn ? '+' : '-' }} {{ number_format($t['amount'], 2, ',', '.') }}</td>
                    <td class="text-right {{ $t['balance'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($t['balance'], 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:#999;">Nenhuma movimentacao encontrada no periodo</td></tr>
            @endforelse
            @if(count($transactions) > 0)
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAIS</strong></td>
                    <td class="text-right positive">+ {{ number_format($totalIn, 2, ',', '.') }}</td>
                    <td class="text-right negative">- {{ number_format($totalOut, 2, ',', '.') }}</td>
                    <td class="text-right {{ $netFlow >= 0 ? 'positive' : 'negative' }}">{{ number_format($netFlow, 2, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Elite Locadora - Relatorio gerado automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
