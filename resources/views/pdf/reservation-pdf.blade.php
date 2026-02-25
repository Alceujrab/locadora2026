<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reserva #{{ $reservation->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px 30px; }
        .header-table { width: 100%; border: none; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .title-badge { background: #1a1a2e; color: #f7c948; padding: 6px 14px; border-radius: 6px; font-size: 14px; font-weight: bold; display: inline-block; }
        .section { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
        .section .title { font-weight: bold; color: #1a1a2e; font-size: 12px; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        .info-table { width: 100%; border: none; }
        .info-table td { border: none; padding: 3px 6px; vertical-align: top; }
        .info-label { font-weight: bold; color: #555; width: 140px; }
        .info-value { color: #111; }
        .extras-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .extras-table th { background: #1a1a2e; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        .extras-table td { border-bottom: 1px solid #e5e7eb; padding: 5px 8px; font-size: 10px; }
        .total-box { background: #1a1a2e; color: #fff; border-radius: 6px; padding: 12px; margin-top: 12px; text-align: center; }
        .total-box .amount { font-size: 24px; font-weight: bold; color: #f7c948; }
        .footer { margin-top: 25px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 8px; color: #999; text-align: center; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 70px;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" style="max-width: 60px; max-height: 60px;" alt="Logo">
                @endif
            </td>
            <td>
                <span class="title-badge">RESERVA #{{ $reservation->id }}</span>
                <p style="font-size: 10px; color: #888; margin: 4px 0 0 0;">
                    Emitida em {{ now()->format('d/m/Y H:i') }}
                </p>
            </td>
            <td style="text-align: right; width: 200px; font-size: 9px; color: #777;">
                <p>{{ \App\Models\Setting::get('company_name', 'Elite Locadora de Veiculos') }}</p>
                <p>CNPJ: {{ \App\Models\Setting::get('company_cnpj', '00.000.000/0001-00') }}</p>
                <p>Tel: {{ \App\Models\Setting::get('company_phone', '(66) 3521-0000') }}</p>
            </td>
        </tr>
    </table>

    <div class="section">
        <div class="title">Dados do Cliente</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nome:</td>
                <td class="info-value">{{ $reservation->customer?->name ?? '-' }}</td>
                <td class="info-label">CPF/CNPJ:</td>
                <td class="info-value">{{ $reservation->customer?->cpf ?? $reservation->customer?->cnpj ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Telefone:</td>
                <td class="info-value">{{ $reservation->customer?->phone ?? '-' }}</td>
                <td class="info-label">Email:</td>
                <td class="info-value">{{ $reservation->customer?->email ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="title">Veiculo</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Placa:</td>
                <td class="info-value">{{ $reservation->vehicle?->plate ?? '-' }}</td>
                <td class="info-label">Modelo:</td>
                <td class="info-value">{{ $reservation->vehicle?->brand ?? '' }} {{ $reservation->vehicle?->model ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Ano:</td>
                <td class="info-value">{{ $reservation->vehicle?->year ?? '-' }}</td>
                <td class="info-label">Cor:</td>
                <td class="info-value">{{ $reservation->vehicle?->color ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="title">Periodo da Locacao</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Retirada:</td>
                <td class="info-value">{{ $reservation->pickup_date?->format('d/m/Y H:i') }}</td>
                <td class="info-label">Local Retirada:</td>
                <td class="info-value">{{ $reservation->pickupBranch?->name ?? $reservation->branch?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Devolucao:</td>
                <td class="info-value">{{ $reservation->return_date?->format('d/m/Y H:i') }}</td>
                <td class="info-label">Local Devolucao:</td>
                <td class="info-value">{{ $reservation->returnBranch?->name ?? $reservation->branch?->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Total de Dias:</td>
                <td class="info-value"><strong>{{ $reservation->total_days }}</strong></td>
                <td class="info-label">Diaria:</td>
                <td class="info-value"><strong>R$ {{ number_format((float) $reservation->daily_rate, 2, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    @if($reservation->extras && $reservation->extras->count() > 0)
    <div class="section">
        <div class="title">Opcionais / Extras</div>
        <table class="extras-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qtd</th>
                    <th>Valor Unit.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservation->extras as $extra)
                <tr>
                    <td>{{ $extra->rentalExtra?->name ?? 'Extra' }}</td>
                    <td>{{ $extra->quantity }}</td>
                    <td>R$ {{ number_format((float) $extra->unit_price, 2, ',', '.') }}</td>
                    <td>R$ {{ number_format((float) $extra->total, 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="title">Resumo Financeiro</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Subtotal Diarias:</td>
                <td class="info-value">R$ {{ number_format((float) $reservation->subtotal, 2, ',', '.') }}</td>
            </tr>
            @if((float) $reservation->extras_total > 0)
            <tr>
                <td class="info-label">Total Extras:</td>
                <td class="info-value">R$ {{ number_format((float) $reservation->extras_total, 2, ',', '.') }}</td>
            </tr>
            @endif
            @if((float) $reservation->discount > 0)
            <tr>
                <td class="info-label">Desconto:</td>
                <td class="info-value" style="color: #dc2626;">- R$ {{ number_format((float) $reservation->discount, 2, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="total-box">
        <p style="margin: 0; font-size: 11px;">TOTAL DA RESERVA</p>
        <p class="amount" style="margin: 4px 0;">R$ {{ number_format((float) $reservation->total, 2, ',', '.') }}</p>
    </div>

    @if($reservation->notes)
    <div class="section" style="margin-top: 12px;">
        <div class="title">Observacoes</div>
        <p>{{ $reservation->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | Reserva #{{ $reservation->id }}</p>
        <p>{{ \App\Models\Setting::get('invoice_footer', 'Este documento nao possui validade fiscal.') }}</p>
    </div>

</body>
</html>
