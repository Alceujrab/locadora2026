<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>OS #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px 30px; }
        h1 { font-size: 20px; color: #e67e22; margin: 0; }
        h2 { font-size: 13px; color: #555; font-weight: normal; margin: 0; }
        .header { border-bottom: 3px solid #e67e22; padding-bottom: 10px; margin-bottom: 15px; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 13px; font-weight: bold; color: #e67e22; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        .info-table { width: 100%; border: none; margin-bottom: 5px; }
        .info-table td { padding: 3px 5px; vertical-align: top; border: none; }
        .info-label { font-weight: bold; color: #555; width: 150px; }
        .info-value { color: #222; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table.items th { background: #f5f5f5; border: 1px solid #ddd; padding: 5px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #555; }
        table.items td { border: 1px solid #ddd; padding: 5px 8px; }
        .total-row td { font-weight: bold; background: #fef5e7; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; color: #fff; font-weight: bold; }
        .badge-open { background: #3498db; }
        .badge-progress { background: #9b59b6; }
        .badge-signature { background: #f39c12; }
        .badge-completed { background: #27ae60; }
        .badge-closed { background: #7f8c8d; }
        .badge-cancelled { background: #e74c3c; }
        .signature-line { border-bottom: 1px solid #333; width: 200px; margin-top: 40px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <h2>{{ $order->branch?->name ?? 'Elite Locadora' }} | Emissao: {{ now()->format('d/m/Y H:i') }}</h2>
    </div>

    {{-- Dados do Veiculo --}}
    <div class="section">
        <div class="section-title">DADOS DO VEICULO</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Placa:</td>
                <td class="info-value">{{ $order->vehicle?->plate ?? '-' }}</td>
                <td class="info-label">Cidade do Veiculo:</td>
                <td class="info-value">{{ $order->vehicle_city ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Marca/Modelo:</td>
                <td class="info-value">{{ $order->vehicle?->brand ?? '' }} {{ $order->vehicle?->model ?? '' }}</td>
                <td class="info-label">Tel. Motorista:</td>
                <td class="info-value">{{ $order->driver_phone ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Ano:</td>
                <td class="info-value">{{ $order->vehicle?->year ?? '-' }}</td>
                <td class="info-label">Locatario:</td>
                <td class="info-value">{{ $order->customer?->name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Informacoes da OS --}}
    <div class="section">
        <div class="section-title">INFORMACOES DA OS</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Status:</td>
                <td class="info-value">
                    <span class="badge @if($order->status->value === 'aberta') badge-open @elseif($order->status->value === 'em_andamento') badge-progress @elseif($order->status->value === 'aguardando_assinatura') badge-signature @elseif($order->status->value === 'concluida') badge-completed @elseif($order->status->value === 'fechada') badge-closed @else badge-cancelled @endif">{{ $order->status->label() }}</span>
                </td>
                <td class="info-label">Solicitado por:</td>
                <td class="info-value">{{ $order->requested_by ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Tipo:</td>
                <td class="info-value">{{ ucfirst($order->type) }}</td>
                <td class="info-label">Funcionario:</td>
                <td class="info-value">{{ $order->openedByUser?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Abertura:</td>
                <td class="info-value">{{ $order->opened_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td class="info-label">Oficina:</td>
                <td class="info-value">{{ $order->supplier?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Conclusao:</td>
                <td class="info-value">{{ $order->completed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                <td class="info-label">NF/Recibo:</td>
                <td class="info-value">{{ $order->nf_number ?? '-' }}</td>
            </tr>
        </table>
    </div>

    {{-- Descricao do Problema --}}
    <div class="section">
        <div class="section-title">DESCRICAO DO PROBLEMA</div>
        <p>{{ $order->description }}</p>
    </div>

    {{-- Procedimento Adotado --}}
    @if($order->procedure_adopted)
    <div class="section">
        <div class="section-title">PROCEDIMENTO ADOTADO</div>
        <p>{{ $order->procedure_adopted }}</p>
    </div>
    @endif

    {{-- Itens / Pecas e Mao de Obra --}}
    @if($order->items->count() > 0)
    <div class="section">
        <div class="section-title">ITENS / SERVICOS</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Tipo</th>
                    <th>Descricao</th>
                    <th style="text-align:center">Qtd</th>
                    <th style="text-align:right">Valor Unit.</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->type === 'peca' ? 'Peca' : 'Mao de Obra' }}</td>
                    <td>{{ $item->description }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td style="text-align:right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align:right">TOTAL PECAS:</td>
                    <td style="text-align:right">R$ {{ number_format($order->items_total, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align:right">TOTAL MAO DE OBRA:</td>
                    <td style="text-align:right">R$ {{ number_format($order->labor_total, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td colspan="4" style="text-align:right; font-size: 13px;">TOTAL GERAL:</td>
                    <td style="text-align:right; font-size: 13px;">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- Observacoes --}}
    @if($order->notes)
    <div class="section">
        <div class="section-title">OBSERVACOES</div>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    @if($order->closing_notes)
    <div class="section">
        <div class="section-title">OBSERVACOES DE FECHAMENTO</div>
        <p>{{ $order->closing_notes }}</p>
    </div>
    @endif

    {{-- Assinatura --}}
    <div class="section" style="margin-top: 40px;">
        <table class="info-table" style="width: 100%;">
            <tr>
                <td style="width: 45%; text-align: center; padding-top: 50px;">
                    <div class="signature-line" style="margin: 0 auto;"></div>
                    <p style="font-size: 10px; color: #666; margin-top: 5px;">Responsavel pela OS</p>
                </td>
                <td style="width: 10%;"></td>
                <td style="width: 45%; text-align: center; padding-top: 50px;">
                    <div class="signature-line" style="margin: 0 auto;"></div>
                    <p style="font-size: 10px; color: #666; margin-top: 5px;">Locatario / Cliente</p>
                    @if($order->isSigned())
                    <p style="font-size: 9px; color: #27ae60; margin-top: 3px;">
                        Assinado em {{ $order->signed_at?->format('d/m/Y H:i') }}
                        | IP: {{ $order->signature_ip }}
                    </p>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | OS #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
    </div>

</body>
</html>
