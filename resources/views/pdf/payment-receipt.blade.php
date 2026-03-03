<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Recibo de Pagamento Nº {{ str_pad($account->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1a202c;
            background: #fff;
            padding: 25px 30px;
        }

        /* ============ CABEÇALHO ============ */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #10B981; padding-bottom: 12px; margin-bottom: 18px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo { width: 80px; }
        .header-logo img { max-width: 75px; max-height: 65px; }
        .header-title h1 { font-size: 17px; color: #10B981; margin-bottom: 2px; }
        .header-title p  { font-size: 10px; color: #64748b; margin-top: 2px; }
        .header-company  { text-align: right; font-size: 9px; color: #4b5563; line-height: 1.6; }
        .header-company strong { font-size: 11px; color: #1e293b; display: block; margin-bottom: 2px; }

        /* BADGE DE STATUS */
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 10px; font-weight: bold; }
        .status-parcial     { background: #ede9fe; color: #6d28d9; }
        .status-recebido    { background: #d1fae5; color: #065f46; }
        .status-pendente    { background: #fef3c7; color: #92400e; }
        .status-inadimplente{ background: #fee2e2; color: #991b1b; }
        .status-cancelado   { background: #f3f4f6; color: #374151; }

        /* ============ SEÇÕES ============ */
        .section { margin-bottom: 14px; }
        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            color: #10B981;
            letter-spacing: 0.06em;
            border-bottom: 1px solid #d1fae5;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        /* TABELA INFO */
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 3px 4px; border: none; vertical-align: top; }
        .info-label { font-weight: bold; color: #475569; width: 38%; }
        .info-value { color: #1e293b; }

        /* ============ CAIXA DE VALORES ============ */
        .values-box { background: #f0fdf4; border: 1px solid #10B981; border-radius: 5px; padding: 12px; margin-bottom: 14px; }
        .values-table { width: 100%; border-collapse: collapse; }
        .values-table td { padding: 5px 6px; border-bottom: 1px dashed #d1d5db; }
        .values-table tr:last-child td { border-bottom: none; }
        .v-label { font-weight: bold; color: #475569; }
        .v-value { text-align: right; font-weight: bold; color: #1e293b; }
        .v-total td { border-top: 2px solid #10B981; padding-top: 7px; font-size: 12px; }
        .v-total .v-value { color: #10B981; font-size: 14px; }
        .v-remaining .v-value { color: #b45309; }

        /* AVISO PARCIAL */
        .alert-remaining {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-left: 4px solid #f97316;
            border-radius: 0 5px 5px 0;
            padding: 10px 12px;
            margin-bottom: 14px;
            color: #9a3412;
            font-size: 11px;
        }

        /* NOTAS */
        .notes-box {
            background: #fffbeb;
            border-left: 3px solid #f59e0b;
            padding: 8px 12px;
            border-radius: 0 5px 5px 0;
            font-size: 10px;
            color: #78350f;
            margin-bottom: 14px;
        }
        .notes-title { font-weight: bold; margin-bottom: 4px; font-size: 11px; }

        /* ============ DIVISOR ============ */
        .divider { border: none; border-top: 1px dashed #cbd5e1; margin: 14px 0; }

        /* ============ ASSINATURAS ============ */
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 35px; }
        .sign-table td { border: none; text-align: center; vertical-align: bottom; padding: 0 20px; }
        .sign-line { border-top: 1px solid #94a3b8; padding-top: 5px; margin-top: 35px; font-size: 9px; color: #64748b; }

        /* ============ RODAPÉ ============ */
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 25px;
            padding-top: 10px;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
        }
        .footer strong { color: #10B981; }
    </style>
</head>
<body>

    {{-- ===== CABEÇALHO ===== --}}
    <table class="header-table">
        <tr>
            {{-- Logo --}}
            <td class="header-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo {{ $company['name'] }}">
                @endif
            </td>

            {{-- Título do documento --}}
            <td class="header-title" style="padding-left: 12px;">
                <h1>RECIBO DE PAGAMENTO</h1>
                <p>Nº {{ str_pad($account->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p style="margin-top: 5px;">
                    @php
                        $statusLabels = [
                            'parcial'      => 'PAGO PARCIAL',
                            'recebido'     => 'QUITADO',
                            'pendente'     => 'PENDENTE',
                            'inadimplente' => 'INADIMPLENTE',
                            'cancelado'    => 'CANCELADO',
                        ];
                    @endphp
                    <span class="status-badge status-{{ $account->status }}">
                        {{ $statusLabels[$account->status] ?? strtoupper($account->status) }}
                    </span>
                </p>
            </td>

            {{-- Dados da empresa (do banco de dados) --}}
            <td class="header-company">
                <strong>{{ $company['name'] }}</strong>
                @if($company['cnpj'])
                    CNPJ: {{ $company['cnpj'] }}<br>
                @endif
                @if($company['phone'])
                    Tel.: {{ $company['phone'] }}<br>
                @endif
                @if($company['email'])
                    {{ $company['email'] }}<br>
                @endif
                @if($company['address'])
                    {{ $company['address'] }}<br>
                @endif
                @if($company['city'] || $company['state'])
                    {{ $company['city'] }}{{ ($company['city'] && $company['state']) ? ' - ' : '' }}{{ $company['state'] }}
                    @if($company['zip'])
                         CEP: {{ $company['zip'] }}
                    @endif
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== DADOS DO CLIENTE ===== --}}
    <div class="section">
        <div class="section-title">Dados do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Cliente:</td>
                <td class="info-value"><strong>{{ $account->customer->name ?? 'N/A' }}</strong></td>
                <td class="info-label">CPF/CNPJ:</td>
                <td class="info-value">{{ $account->customer->cpf_cnpj ? $account->customer->formatted_cpf_cnpj : '—' }}</td>
            </tr>
            <tr>
                <td class="info-label">Telefone:</td>
                <td class="info-value">{{ $account->customer->phone ?? '—' }}</td>
                <td class="info-label">E-mail:</td>
                <td class="info-value">{{ $account->customer->email ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== DADOS DA COBRANÇA ===== --}}
    <div class="section">
        <div class="section-title">Dados da Cobrança</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Descrição:</td>
                <td class="info-value" colspan="3">{{ $account->description }}</td>
            </tr>
            <tr>
                <td class="info-label">Vencimento:</td>
                <td class="info-value">{{ $account->due_date->format('d/m/Y') }}</td>
                @if($account->contract)
                <td class="info-label">Contrato:</td>
                <td class="info-value">{{ $account->contract->contract_number }}</td>
                @elseif($account->invoice)
                <td class="info-label">Fatura:</td>
                <td class="info-value">{{ $account->invoice->invoice_number }}</td>
                @else
                <td></td><td></td>
                @endif
            </tr>
            @if($account->branch)
            <tr>
                <td class="info-label">Filial:</td>
                <td class="info-value" colspan="3">{{ $account->branch->name }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ===== VALORES ===== --}}
    <div class="values-box">
        <table class="values-table">
            <tr>
                <td class="v-label">Valor Total da Cobrança</td>
                <td class="v-value">R$ {{ number_format((float)$account->amount, 2, ',', '.') }}</td>
            </tr>
            <tr class="v-total">
                <td class="v-label">&#10003; Total Pago</td>
                <td class="v-value">R$ {{ number_format((float)$account->paid_amount, 2, ',', '.') }}</td>
            </tr>
            @if($account->status === 'parcial')
            <tr class="v-remaining">
                <td class="v-label">Saldo Devedor Restante</td>
                <td class="v-value">R$ {{ number_format($account->remaining, 2, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ===== DADOS DO PAGAMENTO ===== --}}
    <div class="section">
        <div class="section-title">Dados do Pagamento Registrado</div>
        <table class="info-table">
            @if($account->received_at)
            <tr>
                <td class="info-label">Data do Recebimento:</td>
                <td class="info-value"><strong>{{ $account->received_at->format('d/m/Y H:i') }}</strong></td>
                <td class="info-label">Forma de Pagamento:</td>
                <td class="info-value">{{ $methodLabels[$account->payment_method] ?? ucfirst($account->payment_method ?? '—') }}</td>
            </tr>
            @endif
            @if($account->payer_name || $account->payment_bank)
            <tr>
                <td class="info-label">Pagador:</td>
                <td class="info-value">{{ $account->payer_name ?? '—' }}</td>
                <td class="info-label">Banco:</td>
                <td class="info-value">{{ $account->payment_bank ?? '—' }}</td>
            </tr>
            @endif
            @if($account->payment_reference)
            <tr>
                <td class="info-label">Comprovante / Ref.:</td>
                <td class="info-value" colspan="3">{{ $account->payment_reference }}</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Emissão do Recibo:</td>
                <td class="info-value" colspan="3">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== AVISO: SALDO DEVEDOR (apenas quando parcial) ===== --}}
    @if($account->status === 'parcial')
    <div class="alert-remaining">
        <strong>⚠ Atenção — Pagamento Parcial</strong><br>
        Existe um saldo devedor de <strong>R$ {{ number_format($account->remaining, 2, ',', '.') }}</strong>.
        Por favor, efetue o pagamento do valor restante para quitar esta cobrança.
    </div>
    @endif

    {{-- ===== HISTÓRICO DE PAGAMENTOS ===== --}}
    @if($account->notes)
    <div class="notes-box">
        <div class="notes-title">📋 Histórico de Pagamentos</div>
        <div style="white-space: pre-wrap;">{{ $account->notes }}</div>
    </div>
    @endif

    <hr class="divider">

    {{-- ===== ASSINATURAS ===== --}}
    <table class="sign-table">
        <tr>
            <td>
                <div class="sign-line">
                    _________________________________<br>
                    Assinatura do Cliente<br>
                    <strong>{{ $account->customer->name ?? '' }}</strong>
                </div>
            </td>
            <td>
                <div class="sign-line">
                    _________________________________<br>
                    Assinatura do Responsável<br>
                    <strong>{{ $company['name'] }}</strong>
                </div>
            </td>
        </tr>
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
