<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura {{ $invoice->invoice_number }} - Elite Locadora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; }
        .container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #1a1a2e, #16213e); color: #fff; padding: 24px; text-align: center; }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 14px; opacity: 0.8; }
        .header .badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-top: 8px; background: rgba(245,158,11,0.3); color: #fbbf24; }
        .content { padding: 24px; }
        .info-group { margin-bottom: 16px; }
        .info-group h3 { font-size: 13px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: 600; color: #111827; }
        .total-card { background: linear-gradient(135deg, #1a1a2e, #0f3460); border-radius: 12px; padding: 20px; text-align: center; margin: 20px 0; }
        .total-card .label { font-size: 11px; text-transform: uppercase; color: #9ca3af; letter-spacing: 1px; }
        .total-card .amount { font-size: 32px; font-weight: 800; color: #f59e0b; margin-top: 4px; }
        .total-card .due { font-size: 13px; color: #d1d5db; margin-top: 6px; }
        .payment-info { background: #f0fdf4; border: 2px solid #86efac; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .payment-info h4 { color: #166534; font-size: 13px; text-transform: uppercase; margin-bottom: 10px; }
        .payment-info .row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .payment-info .row .lbl { color: #4b5563; }
        .payment-info .row .val { font-weight: 700; color: #166534; }
        .origin-info { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 16px; margin: 16px 0; }
        .origin-info h4 { color: #1e40af; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; }
        .pdf-btn { display: block; width: 100%; padding: 12px; background: #3b82f6; color: #fff; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 16px; transition: background 0.2s; }
        .pdf-btn:hover { background: #2563eb; }
        .confirm-section { margin-top: 24px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; }
        .confirm-section h3 { font-size: 16px; margin-bottom: 8px; }
        .confirm-section p { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .confirm-btn { display: inline-block; padding: 14px 40px; background: #16a34a; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; text-decoration: none; transition: background 0.2s; }
        .confirm-btn:hover { background: #15803d; }
        .footer { text-align: center; padding: 16px; font-size: 11px; color: #9ca3af; }
        .already-confirmed { background: #dcfce7; border: 2px solid #86efac; border-radius: 12px; padding: 20px; text-align: center; margin: 16px 0; }
        .already-confirmed .icon { font-size: 40px; }
        .already-confirmed p { color: #166534; font-weight: 600; margin-top: 8px; }
        .already-confirmed .date { font-size: 12px; color: #4b5563; margin-top: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>FATURA {{ $invoice->invoice_number }}</h1>
                <p>Elite Locadora</p>
                <span class="badge">{{ $invoice->status->label() }}</span>
            </div>

            <div class="content">
                {{-- Dados do Cliente --}}
                <div class="info-group">
                    <h3>Cliente</h3>
                    <div class="info-row"><span class="label">Nome</span><span class="value">{{ $invoice->customer?->name ?? '-' }}</span></div>
                    <div class="info-row"><span class="label">CPF/CNPJ</span><span class="value">{{ $invoice->customer?->cpf ?? $invoice->customer?->cnpj ?? '-' }}</span></div>
                </div>

                {{-- Detalhes --}}
                <div class="info-group">
                    <h3>Detalhes da Fatura</h3>
                    <div class="info-row"><span class="label">Vencimento</span><span class="value" style="color: #dc2626;">{{ $invoice->due_date?->format('d/m/Y') }}</span></div>
                    @if($invoice->contract)
                    <div class="info-row"><span class="label">Contrato</span><span class="value">{{ $invoice->contract->contract_number }}</span></div>
                    @endif
                    <div class="info-row"><span class="label">Valor Base</span><span class="value">R$ {{ number_format($invoice->amount, 2, ',', '.') }}</span></div>
                    @if($invoice->penalty_amount > 0)
                    <div class="info-row"><span class="label">Multa</span><span class="value" style="color: #dc2626;">+ R$ {{ number_format($invoice->penalty_amount, 2, ',', '.') }}</span></div>
                    @endif
                    @if($invoice->discount > 0)
                    <div class="info-row"><span class="label">Desconto</span><span class="value" style="color: #16a34a;">- R$ {{ number_format($invoice->discount, 2, ',', '.') }}</span></div>
                    @endif
                </div>

                {{-- Origem (OS) --}}
                @if(isset($serviceOrder) && $serviceOrder)
                <div class="origin-info">
                    <h4>Origem: Ordem de Servico #{{ str_pad($serviceOrder->id, 5, '0', STR_PAD_LEFT) }}</h4>
                    <div class="info-row"><span class="label">Veiculo</span><span class="value">{{ $serviceOrder->vehicle?->plate }}</span></div>
                    <div class="info-row"><span class="label">Descricao</span><span class="value">{{ Str::limit($serviceOrder->description, 60) }}</span></div>
                </div>
                @endif

                {{-- Total --}}
                <div class="total-card">
                    <div class="label">Total a Pagar</div>
                    <div class="amount">R$ {{ number_format($invoice->total, 2, ',', '.') }}</div>
                    <div class="due">Vencimento: {{ $invoice->due_date?->format('d/m/Y') }}</div>
                </div>

                {{-- Dados de Pagamento --}}
                <div class="payment-info">
                    <h4>Dados para Pagamento</h4>
                    <div class="row"><span class="lbl">PIX (CNPJ)</span><span class="val">00.000.000/0001-00</span></div>
                    <div class="row"><span class="lbl">Titular</span><span class="val">Elite Locadora LTDA</span></div>
                    <div class="row"><span class="lbl">Banco</span><span class="val">Banco do Brasil</span></div>
                    <div class="row"><span class="lbl">Agencia/Conta</span><span class="val">0001 / 12345-6</span></div>
                </div>

                {{-- Download PDF --}}
                @if($invoice->pdf_path)
                <a href="{{ route('invoice.pdf', $invoice->id) }}" class="pdf-btn" target="_blank">📄 Baixar Fatura em PDF</a>
                @endif

                {{-- Confirmação de recebimento --}}
                @if($invoice->confirmed_at)
                    <div class="already-confirmed">
                        <div class="icon">✅</div>
                        <p>Recebimento ja confirmado</p>
                        <div class="date">Em {{ $invoice->confirmed_at->format('d/m/Y H:i') }}</div>
                    </div>
                @else
                    <div class="confirm-section">
                        <h3>Confirmar Recebimento</h3>
                        <p>Ao confirmar, voce declara que recebeu e esta ciente desta fatura e dos valores descritos.</p>
                        <form method="POST" action="{{ route('invoice.confirm', $invoice->id) }}">
                            @csrf
                            <button type="submit" class="confirm-btn">✅ Confirmo o Recebimento</button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="footer">
                <p>Elite Locadora | {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
