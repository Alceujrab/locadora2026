<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lucratividade da Frota</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3B82F6; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #3B82F6; }
        .header p { margin: 5px 0; color: #666; }
        .summary { background-color: #f0f9ff; border: 1px solid #3B82F6; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .summary-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
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
        .total-row { background-color: #e0f2fe !important; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lucratividade da Frota</h1>
        <p>Periodo de {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
        <p>Data de Geracao: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="summary">
        <div class="summary-grid">
            <div class="summary-item">
                <label>Receita Total (R$)</label>
                <div class="value green">{{ number_format($totalRevenue, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Despesas Total (R$)</label>
                <div class="value red">{{ number_format($totalExpenses, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Lucro Liquido (R$)</label>
                <div class="value {{ $totalProfit >= 0 ? 'green' : 'red' }}">{{ number_format($totalProfit, 2, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <label>Margem de Lucro</label>
                <div class="value">{{ number_format($totalMargin, 1, ',', '.') }}%</div>
            </div>
            <div class="summary-item">
                <label>Veiculos</label>
                <div class="value">{{ $activeVehicles }}</div>
            </div>
            <div class="summary-item">
                <label>Receita/Veiculo (R$)</label>
                <div class="value">{{ number_format($revenuePerVehicle, 2, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Placa</th>
                <th>Modelo</th>
                <th>Filial</th>
                <th class="text-right">Contratos</th>
                <th class="text-right">Receita (R$)</th>
                <th class="text-right">Despesas (R$)</th>
                <th class="text-right">Lucro (R$)</th>
                <th class="text-right">Margem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicleData as $v)
                <tr>
                    <td><strong>{{ $v['plate'] }}</strong></td>
                    <td>{{ $v['model'] }}</td>
                    <td>{{ $v['branch'] }}</td>
                    <td class="text-right">{{ $v['contracts'] }}</td>
                    <td class="text-right">{{ number_format($v['revenue'], 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($v['expenses'], 2, ',', '.') }}</td>
                    <td class="text-right {{ $v['profit'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($v['profit'], 2, ',', '.') }}</td>
                    <td class="text-right {{ $v['margin'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($v['margin'], 1, ',', '.') }}%</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; color:#999;">Nenhum veiculo encontrado no periodo</td></tr>
            @endforelse
            @if(count($vehicleData) > 0)
                <tr class="total-row">
                    <td colspan="3"><strong>TOTAL</strong></td>
                    <td class="text-right">-</td>
                    <td class="text-right">{{ number_format($totalRevenue, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalExpenses, 2, ',', '.') }}</td>
                    <td class="text-right {{ $totalProfit >= 0 ? 'positive' : 'negative' }}">{{ number_format($totalProfit, 2, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($totalMargin, 1, ',', '.') }}%</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <p>Elite Locadora - Relatorio gerado automaticamente em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
