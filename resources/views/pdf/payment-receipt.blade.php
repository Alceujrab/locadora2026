<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            color: #1a202c;
            background: #fff;
            padding: 30px;
            font-size: 13px;
        }

        /* CABEÇALHO */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #10B981;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header-left  { display: table-cell; width: 60%; vertical-align: middle; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: middle; }
        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #10B981;
            line-height: 1.2;
        }
        .company-subtitle {
            font-size: 11px;
            color: #64748b;
            margin-top: 3px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e293b;
        }
        .receipt-number {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        /* BADGE DE STATUS */
        .status-badge {
            display: inline-block;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }
        .status-parcial    { background: #e9d5ff; color: #6d28d9; }
        .status-recebido   { background: #d1fae5; color: #065f46; }
        .status-pendente   { background: #fef3c7; color: #92400e; }
        .status-inadimplente { background: #fee2e2; color: #991b1b; }

        /* SEÇÕES */
        .section {
            margin-bottom: 18px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            color: #10B981;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #d1fae5;
            padding-bottom: 4px;
            margin-bottom: 10px;
        }

        /* GRID DE INFO */
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #475569;
            padding: 4px 8px 4px 0;
            vertical-align: top;
        }
        .info-value {
            display: table-cell;
            color: #1e293b;
            padding: 4px 0;
            vertical-align: top;
        }

        /* CAIXA DE VALORES */
        .values-box {
            background: #f0fdf4;
            border: 1px solid #10B981;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 18px;
        }
        .values-table {
            width: 100%;
            border-collapse: collapse;
        }
        .values-table td {
            padding: 6px 8px;
            border-bottom: 1px dashed #d1d5db;
        }
        .values-table tr:last-child td {
            border-bottom: none;
        }
        .values-table .label-col {
            font-weight: bold;
            color: #475569;
        }
        .values-table .value-col {
            text-align: right;
            font-weight: bold;
            color: #1e293b;
        }
        .values-table .total-row td {
            border-top: 2px solid #10B981;
            padding-top: 8px;
            font-size: 14px;
        }
        .values-table .total-row .value-col {
            color: #10B981;
            font-size: 16px;
        }
        .values-table .remaining-row .value-col {
            color: #d97706;
        }

        /* NOTAS */
        .notes-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 10px 14px;
            border-radius: 0 6px 6px 0;
            font-size: 12px;
            color: #78350f;
            margin-bottom: 18px;
        }
        .notes-box .notes-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* AVISO SALDO DEVEDOR */
        .alert-remaining {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 18px;
            color: #9a3412;
        }
        .alert-remaining strong {
            font-size: 14px;
        }

        /* RODAPÉ */
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 30px;
            padding-top: 15px;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
        }
        .footer .footer-company {
            font-weight: bold;
            color: #10B981;
            font-size: 12px;
        }

        /* ASSINATURA */
        .signature-area {
            display: table;
            width: 100%;
            margin-top: 40px;
        }
        .signature-left  { display: table-cell; width: 45%; text-align: center; }
        .signature-right { display: table-cell; width: 45%; text-align: center; margin-left: auto; }
        .signature-line {
            border-top: 1px solid #94a3b8;
            padding-top: 6px;
            margin-top: 40px;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>

    {{-- CABEÇALHO --}}
    <div class="header">
        <div class="header-left">
            <div class="company-name">Elite Locadora</div>
            <div class="company-subtitle">Sistema de Gestão de Locação de Veículos</div>
            @if($account->branch)
                <div class="company-subtitle" style="margin-top:2px;">Filial: {{ $account->branch->name }}</div>
            @endif
        </div>
        <div class="header-right">
            <div class="receipt-title">RECIBO DE PAGAMENTO</div>
            <div class="receipt-number">Nº {{ str_pad($account->id, 6, '0', STR_PAD_LEFT) }}</div>
            @php
                $statusLabels = [
                    'parcial'     => 'PAGO PARCIAL',
                    'recebido'    => 'QUITADO',
                    'pendente'    => 'PENDENTE',
                    'inadimplente'=> 'INADIMPLENTE',
                    'cancelado'   => 'CANCELADO',
                ];
            @endphp
            <div>
                <span class="status-badge status-{{ $account->status }}">
                    {{ $statusLabels[$account->status] ?? strtoupper($account->status) }}
                </span>
            </div>
        </div>
    </div>

    {{-- DADOS DO CLIENTE --}}
    <div class="section">
        <div class="section-title">Dados do Cliente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Cliente:</div>
                <div class="info-value">{{ $account->customer->name ?? 'N/A' }}</div>
            </div>
            @if($account->customer && $account->customer->cpf_cnpj)
            <div class="info-row">
                <div class="info-label">CPF/CNPJ:</div>
                <div class="info-value">{{ $account->customer->formatted_cpf_cnpj }}</div>
            </div>
            @endif
            @if($account->customer && $account->customer->phone)
            <div class="info-row">
                <div class="info-label">Telefone:</div>
                <div class="info-value">{{ $account->customer->phone }}</div>
            </div>
            @endif
            @if($account->customer && $account->customer->email)
            <div class="info-row">
                <div class="info-label">E-mail:</div>
                <div class="info-value">{{ $account->customer->email }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- DADOS DA COBRANÇA --}}
    <div class="section">
        <div class="section-title">Dados da Cobrança</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Descrição:</div>
                <div class="info-value">{{ $account->description }}</div>
            </div>
            @if($account->contract)
            <div class="info-row">
                <div class="info-label">Contrato:</div>
                <div class="info-value">{{ $account->contract->contract_number }}</div>
            </div>
            @endif
            @if($account->invoice)
            <div class="info-row">
                <div class="info-label">Fatura:</div>
                <div class="info-value">{{ $account->invoice->invoice_number }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Vencimento:</div>
                <div class="info-value">{{ $account->due_date->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    {{-- VALORES --}}
    <div class="section">
        <div class="section-title">Valores</div>
        <div class="values-box">
            <table class="values-table">
                <tr>
                    <td class="label-col">Valor Total da Cobrança</td>
                    <td class="value-col">R$ {{ number_format((float)$account->amount, 2, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label-col">Total Pago</td>
                    <td class="value-col">R$ {{ number_format((float)$account->paid_amount, 2, ',', '.') }}</td>
                </tr>
                @if($account->status === 'parcial')
                <tr class="remaining-row">
                    <td class="label-col">Saldo Devedor</td>
                    <td class="value-col">R$ {{ number_format($account->remaining, 2, ',', '.') }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    {{-- DADOS DO PAGAMENTO --}}
    <div class="section">
        <div class="section-title">Dados do Último Pagamento</div>
        <div class="info-grid">
            @if($account->payment_method)
            <div class="info-row">
                <div class="info-label">Forma de Pagamento:</div>
                <div class="info-value">{{ $methodLabels[$account->payment_method] ?? ucfirst($account->payment_method) }}</div>
            </div>
            @endif
            @if($account->received_at)
            <div class="info-row">
                <div class="info-label">Data do Recebimento:</div>
                <div class="info-value">{{ $account->received_at->format('d/m/Y H:i') }}</div>
            </div>
            @endif
            @if($account->payer_name)
            <div class="info-row">
                <div class="info-label">Pagador:</div>
                <div class="info-value">{{ $account->payer_name }}</div>
            </div>
            @endif
            @if($account->payment_bank)
            <div class="info-row">
                <div class="info-label">Banco:</div>
                <div class="info-value">{{ $account->payment_bank }}</div>
            </div>
            @endif
            @if($account->payment_reference)
            <div class="info-row">
                <div class="info-label">Comprovante / Ref.:</div>
                <div class="info-value">{{ $account->payment_reference }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Data de Emissão do Recibo:</div>
                <div class="info-value">{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    {{-- AVISO DE SALDO DEVEDOR (apenas quando parcial) --}}
    @if($account->status === 'parcial')
    <div class="alert-remaining">
        &#9888; <strong>Atenção:</strong> Existe um saldo devedor de
        <strong>R$ {{ number_format($account->remaining, 2, ',', '.') }}</strong>
        referente a esta cobrança. Por favor, efetue o pagamento do saldo restante para quitar a dívida.
    </div>
    @endif

    {{-- NOTAS / HISTÓRICO --}}
    @if($account->notes)
    <div class="notes-box">
        <div class="notes-title">&#128221; Histórico de Pagamentos:</div>
        <div style="white-space: pre-wrap;">{{ $account->notes }}</div>
    </div>
    @endif

    {{-- ASSINATURA --}}
    <div class="signature-area">
        <div class="signature-left">
            <div class="signature-line">
                ___________________________________<br>
                Assinatura do Cliente<br>
                {{ $account->customer->name ?? '' }}
            </div>
        </div>
        <div class="signature-right" style="text-align:right;">
            <div class="signature-line">
                ___________________________________<br>
                Assinatura do Responsável<br>
                Elite Locadora
            </div>
        </div>
    </div>

    {{-- RODAPÉ --}}
    <div class="footer">
        <div class="footer-company">Elite Locadora</div>
        <div>Este documento é um comprovante de pagamento gerado eletronicamente pelo sistema de gestão.</div>
        <div>Gerado em: {{ now()->format('d/m/Y \à\s H:i:s') }}</div>
    </div>

</body>
</html>
