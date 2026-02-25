<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OS Assinada - Elite Locadora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { max-width: 480px; margin: 20px; background: #fff; border-radius: 16px; box-shadow: 0 4px 30px rgba(0,0,0,0.1); text-align: center; overflow: hidden; }
        .icon-area { background: linear-gradient(135deg, #16a34a, #15803d); padding: 40px 20px; }
        .icon-area .check { font-size: 64px; }
        .icon-area h1 { color: #fff; font-size: 22px; margin-top: 12px; }
        .body { padding: 24px; }
        .body p { color: #6b7280; font-size: 14px; line-height: 1.6; margin-bottom: 12px; }
        .info { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; text-align: left; margin: 16px 0; }
        .info div { display: flex; justify-content: space-between; padding: 4px 0; font-size: 13px; }
        .info .label { color: #6b7280; }
        .info .value { font-weight: 600; color: #166534; }
        .btn { display: inline-block; padding: 12px 24px; background: #3b82f6; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 8px; }
        .footer { padding: 16px; font-size: 11px; color: #9ca3af; border-top: 1px solid #f3f4f6; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-area">
            <div class="check">✅</div>
            <h1>OS Assinada com Sucesso!</h1>
        </div>
        <div class="body">
            <p>A Ordem de Servico <strong>#{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</strong> foi assinada digitalmente.</p>

            <div class="info">
                <div><span class="label">Veiculo</span><span class="value">{{ $order->vehicle?->plate ?? '-' }}</span></div>
                <div><span class="label">Assinado em</span><span class="value">{{ $order->signed_at?->format('d/m/Y H:i') }}</span></div>
                <div><span class="label">IP</span><span class="value">{{ $order->signature_ip }}</span></div>
                <div><span class="label">Hash</span><span class="value" style="font-size:10px;">{{ substr($order->signature_hash ?? '', 0, 20) }}...</span></div>
            </div>

            <a href="{{ route('os.signature.pdf', $order->id) }}" class="btn">📄 Baixar PDF da OS</a>
        </div>
        <div class="footer">Elite Locadora | Assinatura digital</div>
    </div>
</body>
</html>
