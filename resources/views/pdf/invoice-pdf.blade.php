<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fatura {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px 30px; }
        h1 { font-size: 20px; color: #e67e22; margin: 0; }
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
        table.items th { background: #f5f5f5; border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #555; }
        table.items td { border: 1px solid #ddd; padding: 6px 8px; }
        .total-row td { font-weight: bold; background: #fef5e7; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 10px; color: #fff; font-weight: bold; }
        .badge-open { background: #3498db; }
        .badge-paid { background: #27ae60; }
        .badge-overdue { background: #e74c3c; }
        .badge-cancelled { background: #95a5a6; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
        .total-box { background: #1a1a2e; color: #fff; border-radius: 6px; padding: 16px; text-align: center; margin: 15px 0; }
        .total-box .label { font-size: 11px; text-transform: uppercase; color: #9ca3af; }
        .total-box .amount { font-size: 28px; font-weight: bold; color: #f59e0b; margin-top: 4px; }
        .total-box .due { font-size: 11px; color: #d1d5db; margin-top: 4px; }
        .payment-box { background: #f0fdf4; border: 2px solid #86efac; border-radius: 6px; padding: 14px; margin: 15px 0; }
        .payment-box .title { font-size: 12px; font-weight: bold; color: #166534; margin-bottom: 8px; text-transform: uppercase; }
        .origin-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 12px; margin: 10px 0; }
        .origin-box .title { font-size: 11px; font-weight: bold; color: #1e40af; margin-bottom: 6px; }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 80px;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-width: 70px; max-height: 70px;" alt="Logo">
                    @endif
                </td>
                <td>
                    <h1>FATURA {{ $invoice->invoice_number }}</h1>
                    <h2>{{ $invoice->branch?->name ?? 'Elite Locadora' }}</h2>
                    <p style="font-size: 10px; color: #888; margin-top: 2px;">
                        Emissao: {{ $invoice->created_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}
                    </p>
                </td>
                <td style="text-align: right; width: 200px; font-size: 9px; color: #777;">
                    <p>{{ \App\Models\Setting::get('company_name', 'Elite Locadora de Veiculos') }}</p>
                    <p>CNPJ: {{ \App\Models\Setting::get('company_cnpj', '00.000.000/0001-00') }}</p>
                    <p>Tel: {{ \App\Models\Setting::get('company_phone', '(66) 3521-0000') }}</p>
                    <p>{{ \App\Models\Setting::get('company_email', 'contato@elitelocadora.com.br') }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- DADOS DO CLIENTE --}}
    <div class="section">
        <div class="section-title">DADOS DO CLIENTE</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nome:</td>
                <td class="info-value">{{ $invoice->customer?->name ?? '-' }}</td>
                <td class="info-label">CPF/CNPJ:</td>
                <td class="info-value">{{ $invoice->customer?->cpf ?? $invoice->customer?->cnpj ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Telefone:</td>
                <td class="info-value">{{ $invoice->customer?->phone ?? '-' }}</td>
                <td class="info-label">Email:</td>
                <td class="info-value">{{ $invoice->customer?->email ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Endereco:</td>
                <td class="info-value" colspan="3">{{ $invoice->customer?->address ?? '-' }}, {{ $invoice->customer?->city ?? '' }} - {{ $invoice->customer?->state ?? '' }}</td>
            </tr>
        </table>
    </div>

    {{-- DADOS DA FATURA --}}
    <div class="section">
        <div class="section-title">DETALHES DA FATURA</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Fatura:</td>
                <td class="info-value"><strong>{{ $invoice->invoice_number }}</strong></td>
                <td class="info-label">Status:</td>
                <td class="info-value">
                    @php
                        $badgeColors = ['aberta' => 'badge-open', 'paga' => 'badge-paid', 'vencida' => 'badge-overdue', 'cancelada' => 'badge-cancelled'];
                    @endphp
                    <span class="badge {{ $badgeColors[$invoice->status->value] ?? 'badge-open' }}">{{ $invoice->status->label() }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Vencimento:</td>
                <td class="info-value" style="font-weight: bold; color: #dc2626;">{{ $invoice->due_date?->format('d/m/Y') }}</td>
                <td class="info-label">Contrato:</td>
                <td class="info-value">{{ $invoice->contract?->contract_number ?? '-' }}</td>
            </tr>
            @if($invoice->installment_number)
            <tr>
                <td class="info-label">Parcela:</td>
                <td class="info-value">{{ $invoice->installment_number }} de {{ $invoice->total_installments ?? '?' }}</td>
                <td></td><td></td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ORIGEM DA FATURA (quando vem de uma OS) --}}
    @if(isset($serviceOrder) && $serviceOrder)
    <div class="origin-box">
        <div class="title">ORIGEM: ORDEM DE SERVICO #{{ str_pad($serviceOrder->id, 5, '0', STR_PAD_LEFT) }}</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Veiculo:</td>
                <td class="info-value">{{ $serviceOrder->vehicle?->plate ?? '-' }} - {{ $serviceOrder->vehicle?->brand ?? '' }} {{ $serviceOrder->vehicle?->model ?? '' }}</td>
            </tr>
            <tr>
                <td class="info-label">Descricao:</td>
                <td class="info-value">{{ $serviceOrder->description }}</td>
            </tr>
            <tr>
                <td class="info-label">Total OS:</td>
                <td class="info-value">R$ {{ number_format($serviceOrder->total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ITENS DA FATURA --}}
    @if($invoice->items && $invoice->items->count() > 0)
    <div class="section">
        <div class="section-title">ITENS</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Descricao</th>
                    <th style="text-align:center">Qtd</th>
                    <th style="text-align:right">Valor Unit.</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td style="text-align:right">R$ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- RESUMO DE VALORES --}}
    <div class="section">
        <div class="section-title">RESUMO FINANCEIRO</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Valor Base:</td>
                <td class="info-value">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</td>
            </tr>
            @if($invoice->penalty_amount > 0)
            <tr>
                <td class="info-label">Multa (2%):</td>
                <td class="info-value" style="color: #dc2626;">+ R$ {{ number_format($invoice->penalty_amount, 2, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->interest_amount > 0)
            <tr>
                <td class="info-label">Juros:</td>
                <td class="info-value" style="color: #dc2626;">+ R$ {{ number_format($invoice->interest_amount, 2, ',', '.') }}</td>
            </tr>
            @endif
            @if($invoice->discount > 0)
            <tr>
                <td class="info-label">Desconto:</td>
                <td class="info-value" style="color: #16a34a;">- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- TOTAL EM DESTAQUE --}}
    <div class="total-box">
        <div class="label">Total a Pagar</div>
        <div class="amount">R$ {{ number_format($invoice->total, 2, ',', '.') }}</div>
        <div class="due">Vencimento: {{ $invoice->due_date?->format('d/m/Y') }}</div>
    </div>

    {{-- INFORMAÇÕES DE PAGAMENTO --}}
    <div class="payment-box">
        <div class="title">Dados para Pagamento</div>
        <table class="info-table">
            <tr>
                <td class="info-label">PIX ({{ \App\Models\Setting::get('pix_type', 'CNPJ') }}):</td>
                <td class="info-value"><strong>{{ \App\Models\Setting::get('pix_key', '00.000.000/0001-00') }}</strong></td>
            </tr>
            <tr>
                <td class="info-label">Titular:</td>
                <td class="info-value">{{ \App\Models\Setting::get('pix_holder', 'Elite Locadora de Veiculos LTDA') }}</td>
            </tr>
            <tr>
                <td class="info-label">Banco:</td>
                <td class="info-value">{{ \App\Models\Setting::get('bank_name', 'Banco do Brasil') }} - Ag {{ \App\Models\Setting::get('bank_agency', '0001') }} / CC {{ \App\Models\Setting::get('bank_account', '12345-6') }}</td>
            </tr>
        </table>
        <p style="font-size: 9px; color: #6b7280; margin-top: 8px;">{{ \App\Models\Setting::get('invoice_terms', 'Apos o pagamento, envie o comprovante pelo WhatsApp ou email.') }}</p>
    </div>

    @if($invoice->notes)
    <div class="section">
        <div class="section-title">OBSERVACOES</div>
        <p>{{ $invoice->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | Fatura {{ $invoice->invoice_number }}</p>
        <p style="margin-top: 4px;">{{ \App\Models\Setting::get('invoice_footer', 'Este documento nao possui validade fiscal. Para nota fiscal, solicite a NFS-e.') }}</p>
    </div>

</body>
</html>
