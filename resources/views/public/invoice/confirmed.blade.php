<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fatura Confirmada - Elite Locadora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .container { max-width: 480px; padding: 20px; text-align: center; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 40px 30px; }
        .icon { font-size: 64px; margin-bottom: 16px; }
        h1 { font-size: 22px; color: #166534; margin-bottom: 8px; }
        p { font-size: 14px; color: #4b5563; line-height: 1.6; }
        .fatura-info { background: #f0fdf4; border-radius: 10px; padding: 16px; margin: 20px 0; }
        .fatura-info .number { font-size: 18px; font-weight: 800; color: #166534; }
        .fatura-info .detail { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .back-btn { display: inline-block; padding: 12px 30px; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 20px; transition: background 0.2s; }
        .back-btn:hover { background: #2563eb; }
        .footer { margin-top: 20px; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">✅</div>
            <h1>{{ $message ?? 'Recebimento Confirmado!' }}</h1>
            <p>Obrigado por confirmar o recebimento da fatura. Este registro sera mantido para auditoria.</p>

            <div class="fatura-info">
                <div class="number">{{ $invoice->invoice_number }}</div>
                <div class="detail">Total: R$ {{ number_format($invoice->total, 2, ',', '.') }} | Vencimento: {{ $invoice->due_date?->format('d/m/Y') }}</div>
                <div class="detail">Confirmado em: {{ $invoice->confirmed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }} | IP: {{ $invoice->confirmation_ip ?? request()->ip() }}</div>
            </div>

            @if($invoice->pdf_path)
            <a href="{{ route('invoice.pdf', $invoice->id) }}" class="back-btn" target="_blank">📄 Baixar PDF da Fatura</a>
            @endif

            <div class="footer">
                <p>Elite Locadora | {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
