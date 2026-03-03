<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Faturas a Receber</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px 25px; font-size: 10px; }

        /* CABEÇALHO */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #3B82F6; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo img { max-width: 65px; max-height: 55px; }
        .header-title h1 { font-size: 16px; color: #3B82F6; margin: 0 0 2px 0; }
        .header-title p { font-size: 9px; color: #64748b; margin: 2px 0; }
        .header-company { text-align: right; font-size: 8px; color: #4b5563; line-height: 1.5; }
        .header-company strong { font-size: 10px; color: #1e293b; display: block; margin-bottom: 2px; }

        /* RESUMO */
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .summary-table td { padding: 8px 10px; text-align: center; border: 1px solid #bfdbfe; background: #eff6ff; }
        .summary-table .s-label { font-size: 8px; font-weight: bold; text-transform: uppercase; color: #475569; display: block; margin-bottom: 3px; }
        .summary-table .s-value { font-size: 14px; font-weight: bold; color: #3B82F6; }

        /* TABELA */
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9px; }
        table.data thead { background-color: #3B82F6; color: white; }
        table.data th { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data td { border: 1px solid #ddd; padding: 5px 8px; }
        table.data tbody tr:nth-child(even) { background-color: #f9fafb; }
        .text-right { text-align: right; }

        /* STATUS */
        .status-aberta    { background: #dbeafe; color: #1e40af; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-vencida   { background: #fee2e2; color: #991b1b; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-paga      { background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }
        .status-cancelada { background: #f3f4f6; color: #374151; padding: 2px 6px; border-radius: 3px; font-weight: bold; font-size: 8px; }

        /* RODAPÉ */
        .footer { margin-top: 25px; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; color: #94a3b8; font-size: 8px; }
        .footer strong { color: #3B82F6; }
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
                <h1>RELATÓRIO DE FATURAS A RECEBER</h1>
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
                <span class="s-label">Total Geral</span>
                <span class="s-value">R$ {{ number_format($totals['total'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Abertas</span>
                <span class="s-value">R$ {{ number_format($totals['open'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Vencidas</span>
                <span class="s-value" style="color:#dc2626;">R$ {{ number_format($totals['overdue'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Pagas</span>
                <span class="s-value" style="color:#059669;">R$ {{ number_format($totals['paid'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Canceladas</span>
                <span class="s-value" style="color:#6b7280;">R$ {{ number_format($totals['cancelled'], 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="s-label">Recebimento</span>
                <span class="s-value">{{ $totals['total'] > 0 ? number_format(($totals['paid'] / $totals['total']) * 100, 1) : 0 }}%</span>
            </td>
        </tr>
    </table>

    {{-- ===== TABELA DETALHADA ===== --}}
    <table class="data">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Veículo</th>
                <th>Período Reserva</th>
                <th>Contrato</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th class="text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $statusStr = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;
                    // Placa do veículo
                    $plate = $invoice->contract?->vehicle?->plate;
                    if (!$plate && $invoice->notes && preg_match('/Veiculo:\s*([A-Z0-9]{7})/i', $invoice->notes, $m)) {
                        $plate = strtoupper($m[1]);
                    }
                    // Período da reserva
                    $period = null;
                    if ($invoice->notes && preg_match('/Periodo:\s*(.+?)(?:\n|$)/i', $invoice->notes, $mp)) {
                        $period = trim($mp[1]);
                    }
                @endphp
                <tr>
                    <td style="font-weight:bold;">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td style="font-weight:bold; color:#475569;">{{ $plate ?? '—' }}</td>
                    <td style="font-size:8px;">{{ $period ?? '—' }}</td>
                    <td>{{ $invoice->contract->contract_number ?? '—' }}</td>
                    <td>{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td>
                        <span class="status-{{ strtolower($statusStr) }}">
                            {{ ucfirst($statusStr) }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight:bold;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #999; padding: 15px;">Nenhuma fatura encontrada</td>
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
