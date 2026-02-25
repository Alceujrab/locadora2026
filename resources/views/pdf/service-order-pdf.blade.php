<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>OS #{{ $order->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px 30px; }
        h1 { font-size: 18px; color: #e67e22; margin: 0; }
        h2 { font-size: 12px; color: #555; font-weight: normal; margin: 0; }
        .header { border-bottom: 3px solid #e67e22; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
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
        .badge-auth { background: #f39c12; }
        .badge-authorized { background: #2ecc71; }
        .badge-approval { background: #f39c12; }
        .badge-completed { background: #27ae60; }
        .badge-invoiced { background: #95a5a6; }
        .badge-closed { background: #7f8c8d; }
        .badge-cancelled { background: #e74c3c; }
        .signature-line { border-bottom: 1px solid #333; width: 200px; margin-top: 40px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
        .signature-img { max-width: 200px; max-height: 80px; margin-top: 10px; }
        .charge-box { background: #fef2f2; border: 2px solid #fca5a5; padding: 10px; text-align: center; border-radius: 4px; margin: 10px 0; }
        .charge-amount { font-size: 20px; font-weight: bold; color: #dc2626; }
    </style>
</head>
<body>

    {{-- Header com Logo --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 80px;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-width: 70px; max-height: 70px;" alt="Logo">
                    @endif
                </td>
                <td>
                    <h1>ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
                    <h2>{{ $order->branch?->name ?? 'Elite Locadora' }}</h2>
                    <p style="font-size: 10px; color: #888; margin-top: 2px;">
                        {{ isset($stage) && $stage === 'autorizacao' ? 'AUTORIZACAO DE ABERTURA' : (isset($stage) && $stage === 'conclusao' ? 'APROVACAO DE CONCLUSAO' : 'ORDEM DE SERVICO') }}
                        | Emissao: {{ now()->format('d/m/Y H:i') }}
                    </p>
                </td>
                <td style="text-align: right; width: 200px; font-size: 9px; color: #777;">
                    <p>CNPJ: {{ \App\Models\Setting::get('company_cnpj', '00.000.000/0001-00') }}</p>
                    <p>Tel: {{ \App\Models\Setting::get('company_phone', '(66) 3521-0000') }}</p>
                    <p>{{ \App\Models\Setting::get('company_email', 'contato@elitelocadora.com.br') }}</p>
                </td>
            </tr>
        </table>
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
                    @php
                        $badgeMap = [
                            'aberta' => 'badge-open',
                            'aguardando_autorizacao' => 'badge-auth',
                            'autorizada' => 'badge-authorized',
                            'em_andamento' => 'badge-progress',
                            'aguardando_aprovacao' => 'badge-approval',
                            'concluida' => 'badge-completed',
                            'faturada' => 'badge-invoiced',
                            'fechada' => 'badge-closed',
                            'cancelada' => 'badge-cancelled',
                        ];
                    @endphp
                    <span class="badge {{ $badgeMap[$order->status->value] ?? 'badge-open' }}">{{ $order->status->label() }}</span>
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
        </table>
    </div>

    {{-- Descricao --}}
    <div class="section">
        <div class="section-title">DESCRICAO DO PROBLEMA</div>
        <p>{{ $order->description }}</p>
    </div>

    @if($order->procedure_adopted)
    <div class="section">
        <div class="section-title">PROCEDIMENTO ADOTADO</div>
        <p>{{ $order->procedure_adopted }}</p>
    </div>
    @endif

    {{-- Itens --}}
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
                    <td colspan="4" style="text-align:right">TOTAL:</td>
                    <td style="text-align:right">R$ {{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- Valor cobrado do cliente --}}
    @if($order->customer_charge > 0)
    <div class="charge-box">
        <p style="font-size: 10px; color: #991b1b; font-weight: bold; text-transform: uppercase;">Valor Cobrado do Cliente</p>
        <p class="charge-amount">R$ {{ number_format($order->customer_charge, 2, ',', '.') }}</p>
    </div>
    @endif

    @if($order->notes)
    <div class="section">
        <div class="section-title">OBSERVACOES</div>
        <p>{{ $order->notes }}</p>
    </div>
    @endif

    {{-- ASSINATURAS DUPLAS --}}
    <div class="section" style="margin-top: 30px;">
        <div class="section-title">ASSINATURAS</div>
        <table class="info-table" style="width: 100%;">
            <tr>
                {{-- Assinatura de Autorização --}}
                <td style="width: 45%; text-align: center; padding-top: 10px;">
                    @if(!empty($authSignatureBase64))
                        <img src="{{ $authSignatureBase64 }}" class="signature-img" style="margin: 0 auto;">
                        <p style="font-size: 10px; color: #666; margin-top: 5px;">Autorizacao de Abertura</p>
                        <p style="font-size: 9px; color: #2563eb;">{{ $order->authorization_signed_at?->format('d/m/Y H:i') }} | IP: {{ $order->authorization_ip }}</p>
                        @if($order->authorization_latitude)
                            <p style="font-size: 8px; color: #6b7280;">GPS: {{ $order->authorization_latitude }}, {{ $order->authorization_longitude }}</p>
                        @endif
                    @else
                        <div style="padding-top: 40px;">
                            <div class="signature-line" style="margin: 0 auto;"></div>
                            <p style="font-size: 10px; color: #666; margin-top: 5px;">Autorizacao de Abertura</p>
                            <p style="font-size: 9px; color: #999;">Pendente</p>
                        </div>
                    @endif
                </td>
                <td style="width: 10%;"></td>
                {{-- Assinatura de Conclusão --}}
                <td style="width: 45%; text-align: center; padding-top: 10px;">
                    @if(!empty($completionSignatureBase64))
                        <img src="{{ $completionSignatureBase64 }}" class="signature-img" style="margin: 0 auto;">
                        <p style="font-size: 10px; color: #666; margin-top: 5px;">Aprovacao de Conclusao</p>
                        <p style="font-size: 9px; color: #16a34a;">{{ $order->completion_signed_at?->format('d/m/Y H:i') }} | IP: {{ $order->completion_ip }}</p>
                        @if($order->completion_latitude)
                            <p style="font-size: 8px; color: #6b7280;">GPS: {{ $order->completion_latitude }}, {{ $order->completion_longitude }}</p>
                        @endif
                    @else
                        <div style="padding-top: 40px;">
                            <div class="signature-line" style="margin: 0 auto;"></div>
                            <p style="font-size: 10px; color: #666; margin-top: 5px;">Aprovacao de Conclusao</p>
                            <p style="font-size: 9px; color: #999;">Pendente</p>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | OS #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
    </div>

</body>
</html>
