<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Contas a Receber</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px 25px; font-size: 10px; }

        /* CABEÇALHO */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #10B981; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo img { max-width: 65px; max-height: 55px; }
        .header-title h1 { font-size: 16px; color: #10B981; margin: 0 0 2px 0; }
        .header-title p { font-size: 9px; color: #64748b; margin: 2px 0; }
        .header-company { text-align: right; font-size: 8px; color: #4b5563; line-height: 1.5; }
        .header-company strong { font-size: 10px; color: #1e293b; display: block; margin-bottom: 2px; }

        /* RESUMO */
        .summary { margin-bottom: 15px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 8px 10px; text-align: center; border: 1px solid #d1fae5; background: #f0fdf4; }
        .summary-table .s-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #475569; display: block; margin-bottom: 3px; }
        .summary-table .s-value { font-size: 14px; font-weight: bold; color: #10B981; }

        /* TABELA DE DADOS */
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
        table.data thead { background-color: #10B981; color: white; }
        table.data th { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data td { border: 1px solid #ddd; padding: 5px 8px; }
        table.data tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }

        /* STATUS BADGES */
        .status-pendente     { background: #fef3c7; color: #92400e; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-parcial      { background: #e9d5ff; color: #581c87; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-recebido     { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-inadimplente { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-cancelado    { background: #f3f4f6; color: #374151; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }

        /* RODAPÉ */
        .footer { margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; color: #94a3b8; font-size: 8px; }
        .footer strong { color: #10B981; }
    </style>
</head>
<body>

    {{-- ===== CABEÇALHO COM LOGO + EMPRESA ===== --}}
    <table class="header-table">
        <tr>
            <td style="width: 70px;" class="header-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="header-title" style="padding-left: 10px;">
                <h1>RELATÓRIO DE CONTAS A RECEBER</h1>
                <p>Período: {{ $dateFrom->format('d/m/Y') }} a {{ $dateTo->format('d/m/Y') }}</p>
                <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
            </td>
            <td class="header-company">
                <strong>{{ $company['name'] }}</strong>
                @if($company['cnpj']) CNPJ: {{ $company['cnpj'] }}<br> @endif
                @if($company['phone']) Tel.: {{ $company['phone'] }}<br> @endif
                @if($company['email']) {{ $company['email'] }}<br> @endif
                @if($company['address']) {{ $company['address'] }}<br> @endif
                @if($company['city'] || $company['state'])
                    {{ $company['city'] }}{{ ($company['city'] && $company['state']) ? ' - ' : '' }}{{ $company['state'] }}
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== RESUMO KPIs ===== --}}
    <table class="summary-table">
        <tr>
            <td>
                <span class="s-label">Total a Receber</span>
                <span class="s-value">R$ {{ number_format($totals['total_amount'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Total Recebido</span>
                <span class="s-value" style="color:#059669;">R$ {{ number_format($totals['total_paid'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Saldo Pendente</span>
                <span class="s-value" style="color:#dc2626;">R$ {{ number_format($totals['total_remaining'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Pendentes</span>
                <span class="s-value" style="color:#d97706;">R$ {{ number_format($totals['pending'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Parciais</span>
                <span class="s-value" style="color:#7c3aed;">R$ {{ number_format($totals['partial'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Arrecadação</span>
                <span class="s-value">{{ $totals['total_amount'] > 0 ? number_format(($totals['total_paid'] / $totals['total_amount']) * 100, 1) : 0 }}%</span>
            </td>
        </tr>
    </table>

    {{-- ===== TABELA DETALHADA ===== --}}
    <table class="data">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Cliente</th>
                <th>Veículo</th>
                <th>Vencimento</th>
                <th class="text-right">Valor Total</th>
                <th class="text-right">Valor Pago</th>
                <th class="text-right">Saldo</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                @php
                    // Placa: via invoice->contract->vehicle ou via invoice->notes
                    $plate = null;
                    if ($record->invoice && $record->invoice->contract && $record->invoice->contract->vehicle) {
                        $plate = $record->invoice->contract->vehicle->plate;
                    } elseif ($record->invoice && $record->invoice->notes && preg_match('/Veiculo:\s*([A-Z0-9]{7})/i', $record->invoice->notes, $m)) {
                        $plate = strtoupper($m[1]);
                    }
                @endphp
                <tr>
                    <td>{{ $record->description }}</td>
                    <td>{{ $record->customer->name ?? 'N/A' }}</td>
                    <td style="font-weight:bold; color:#475569;">{{ $plate ?? '—' }}</td>
                    <td>{{ $record->due_date->format('d/m/Y') }}</td>
                    <td class="text-right">R$ {{ number_format($record->amount, 2, ',', '.') }}</td>
                    <td class="text-right">R$ {{ number_format($record->paid_amount, 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:bold;">R$ {{ number_format($record->amount - $record->paid_amount, 2, ',', '.') }}</td>
                    <td>
                        <span class="status-{{ strtolower(str_replace(' ', '', $record->status)) }}">
                            {{ ucfirst($record->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #999; padding: 15px;">Nenhuma conta a receber encontrada</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== RODAPÉ ===== --}}
    <div class="footer">
        <strong>{{ $company['name'] }}</strong>
        @if($company['cnpj']) &bull; CNPJ: {{ $company['cnpj'] }} @endif
        @if($company['phone']) &bull; Tel.: {{ $company['phone'] }} @endif
        @if($company['email']) &bull; {{ $company['email'] }} @endif
        <br>
        {{ $company['footer'] }}
        <br>
        Gerado em: {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</body>
</html>
