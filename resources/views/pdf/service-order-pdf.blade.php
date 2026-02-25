<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>OS #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; }
        .page { padding: 20px 30px; }
        .header { border-bottom: 3px solid #e67e22; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 20px; color: #e67e22; }
        .header h2 { font-size: 14px; color: #555; font-weight: normal; }
        .info-row { display: flex; margin-bottom: 4px; }
        .label { font-weight: bold; min-width: 160px; color: #555; }
        .value { color: #222; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 13px; font-weight: bold; color: #e67e22; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        table th { background: #f5f5f5; border: 1px solid #ddd; padding: 5px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #555; }
        table td { border: 1px solid #ddd; padding: 5px 8px; font-size: 11px; }
        .total-row td { font-weight: bold; background: #fef5e7; }
        .grid-2 { display: table; width: 100%; }
        .grid-2 .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; color: #fff; font-weight: bold; }
        .badge-open { background: #3498db; }
        .badge-progress { background: #9b59b6; }
        .badge-signature { background: #f39c12; }
        .badge-completed { background: #27ae60; }
        .badge-closed { background: #7f8c8d; }
        .badge-cancelled { background: #e74c3c; }
        .signature-area { margin-top: 40px; border-top: 1px solid #ccc; padding-top: 15px; }
        .signature-line { border-bottom: 1px solid #333; width: 250px; margin-top: 40px; }
        .signature-label { font-size: 10px; color: #666; margin-top: 3px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
<div class="page">
    {{-- Header --}}
    <div class="header">
        <h1>ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
        <h2>{{ $order->branch?->name ?? 'Elite Locadora' }} | Emissao: {{ now()->format('d/m/Y H:i') }}</h2>
    </div>

    {{-- Dados do Veículo e Solicitante --}}
    <div class="section">
        <div class="section-title">DADOS DO VEICULO</div>
        <div class="grid-2">
            <div class="col">
                <div class="info-row"><span class="label">Placa:</span> <span class="value">{{ $order->vehicle?->plate ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Marca/Modelo:</span> <span class="value">{{ $order->vehicle?->brand ?? '' }} {{ $order->vehicle?->model ?? '' }}</span></div>
                <div class="info-row"><span class="label">Ano:</span> <span class="value">{{ $order->vehicle?->year ?? '-' }}</span></div>
                <div class="info-row"><span class="label">KM Atual:</span> <span class="value">{{ number_format($order->vehicle?->current_km ?? 0, 0, ',', '.') }} km</span></div>
            </div>
            <div class="col">
                <div class="info-row"><span class="label">Cidade do Veiculo:</span> <span class="value">{{ $order->vehicle_city ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Tel. Motorista:</span> <span class="value">{{ $order->driver_phone ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Locatario:</span> <span class="value">{{ $order->customer?->full_name ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Informações da OS --}}
    <div class="section">
        <div class="section-title">INFORMACOES DA OS</div>
        <div class="grid-2">
            <div class="col">
                <div class="info-row"><span class="label">Status:</span>
                    <span class="badge
                        @if($order->status->value === 'aberta') badge-open
                        @elseif($order->status->value === 'em_andamento') badge-progress
                        @elseif($order->status->value === 'aguardando_assinatura') badge-signature
                        @elseif($order->status->value === 'concluida') badge-completed
                        @elseif($order->status->value === 'fechada') badge-closed
                        @else badge-cancelled
                        @endif
                    ">{{ $order->status->label() }}</span>
                </div>
                <div class="info-row"><span class="label">Tipo:</span> <span class="value">{{ ucfirst($order->type) }}</span></div>
                <div class="info-row"><span class="label">Abertura:</span> <span class="value">{{ $order->opened_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Conclusao:</span> <span class="value">{{ $order->completed_at?->format('d/m/Y H:i') ?? '-' }}</span></div>
            </div>
            <div class="col">
                <div class="info-row"><span class="label">Solicitado por:</span> <span class="value">{{ $order->requested_by ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Funcionario:</span> <span class="value">{{ $order->openedByUser?->name ?? '-' }}</span></div>
                <div class="info-row"><span class="label">Oficina:</span> <span class="value">{{ $order->supplier?->name ?? '-' }}</span></div>
                <div class="info-row"><span class="label">NF/Recibo:</span> <span class="value">{{ $order->nf_number ?? '-' }}</span></div>
            </div>
        </div>
    </div>

    {{-- Descrição do Problema --}}
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

    {{-- Itens / Peças e Mão de Obra --}}
    @if($order->items->count() > 0)
    <div class="section">
        <div class="section-title">ITENS / SERVICOS</div>
        <table>
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

    {{-- Observações --}}
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
    <div class="signature-area">
        <div class="grid-2">
            <div class="col">
                <div class="signature-line"></div>
                <div class="signature-label">Responsavel pela OS</div>
            </div>
            <div class="col">
                <div class="signature-line"></div>
                <div class="signature-label">Locatario / Cliente</div>
                @if($order->isSigned())
                <div style="margin-top: 5px; font-size: 9px; color: #27ae60;">
                    ✓ Assinado digitalmente em {{ $order->signed_at?->format('d/m/Y H:i') }}
                    | IP: {{ $order->signature_ip }}
                    | Hash: {{ substr($order->signature_hash ?? '', 0, 16) }}...
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | OS #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
    </div>
</div>
</body>
</html>
