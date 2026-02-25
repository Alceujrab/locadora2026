<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assinar OS #{{ $order->id }} - Elite Locadora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; }
        .container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #e67e22, #d35400); color: #fff; padding: 24px; text-align: center; }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 14px; opacity: 0.9; }
        .content { padding: 24px; }
        .info-group { margin-bottom: 16px; }
        .info-group h3 { font-size: 13px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: 600; color: #111827; }
        .description-box { background: #fef3c7; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 0 8px 8px 0; margin: 16px 0; }
        .description-box h4 { font-size: 12px; text-transform: uppercase; color: #92400e; margin-bottom: 4px; }
        .description-box p { font-size: 14px; color: #78350f; }
        .pdf-btn { display: block; width: 100%; padding: 12px; background: #3b82f6; color: #fff; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 16px 0; transition: background 0.2s; }
        .pdf-btn:hover { background: #2563eb; }
        .sign-form { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .sign-form h3 { font-size: 16px; margin-bottom: 12px; color: #111827; }
        .checkbox-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; }
        .checkbox-row input[type="checkbox"] { margin-top: 3px; width: 18px; height: 18px; accent-color: #e67e22; }
        .checkbox-row label { font-size: 13px; color: #4b5563; line-height: 1.5; }
        .sign-btn { display: block; width: 100%; padding: 14px; background: #16a34a; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .sign-btn:hover { background: #15803d; }
        .sign-btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .footer { text-align: center; padding: 16px; font-size: 11px; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>🔧 ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p>Elite Locadora</p>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">✅ {{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">❌ {{ session('error') }}</div>
                @endif

                <div class="info-group">
                    <h3>Veiculo</h3>
                    <div class="info-row"><span class="label">Placa</span><span class="value">{{ $order->vehicle?->plate ?? '-' }}</span></div>
                    <div class="info-row"><span class="label">Modelo</span><span class="value">{{ $order->vehicle?->brand ?? '' }} {{ $order->vehicle?->model ?? '' }}</span></div>
                    <div class="info-row"><span class="label">Cidade</span><span class="value">{{ $order->vehicle_city ?? '-' }}</span></div>
                </div>

                <div class="info-group">
                    <h3>Detalhes da OS</h3>
                    <div class="info-row"><span class="label">Tipo</span><span class="value">{{ ucfirst($order->type) }}</span></div>
                    <div class="info-row"><span class="label">Status</span><span class="value">{{ $order->status->label() }}</span></div>
                    <div class="info-row"><span class="label">Abertura</span><span class="value">{{ $order->opened_at?->format('d/m/Y H:i') }}</span></div>
                    <div class="info-row"><span class="label">Oficina</span><span class="value">{{ $order->supplier?->name ?? '-' }}</span></div>
                </div>

                <div class="description-box">
                    <h4>Descricao do Problema</h4>
                    <p>{{ $order->description }}</p>
                </div>

                @if($order->procedure_adopted)
                <div class="description-box" style="background: #dbeafe; border-color: #3b82f6;">
                    <h4 style="color: #1e40af;">Procedimento Adotado</h4>
                    <p style="color: #1e3a5f;">{{ $order->procedure_adopted }}</p>
                </div>
                @endif

                @if($order->items->count() > 0)
                <div class="info-group">
                    <h3>Itens / Servicos</h3>
                    @foreach($order->items as $item)
                    <div class="info-row">
                        <span class="label">{{ $item->description }} (x{{ $item->quantity }})</span>
                        <span class="value">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="info-row" style="border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                        <span class="label" style="font-weight:700; color:#111;">TOTAL</span>
                        <span class="value" style="font-size: 16px; color: #e67e22;">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                {{-- Download PDF --}}
                <a href="{{ route('os.signature.pdf', $order->id) }}" class="pdf-btn" target="_blank">
                    📄 Baixar PDF da OS
                </a>

                {{-- Formulário de assinatura --}}
                <div class="sign-form">
                    <h3>✍️ Assinatura Digital</h3>
                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 16px;">
                        Ao assinar, voce confirma que esta ciente dos servicos descritos nesta Ordem de Servico.
                    </p>

                    <form method="POST" action="{{ route('os.signature.sign', $order->id) }}">
                        @csrf
                        <div class="checkbox-row">
                            <input type="checkbox" name="accept_terms" id="accept_terms" required>
                            <label for="accept_terms">
                                Li e concordo com os termos desta Ordem de Servico. Estou ciente dos servicos a serem realizados no veiculo acima descrito.
                            </label>
                        </div>

                        @error('accept_terms')
                            <div class="alert alert-error">Voce precisa aceitar os termos para assinar.</div>
                        @enderror

                        <button type="submit" class="sign-btn" id="signBtn" disabled>
                            ✅ Assinar Digitalmente
                        </button>
                    </form>
                </div>
            </div>

            <div class="footer">
                <p>Assinatura digital com validade juridica</p>
                <p>{{ now()->format('d/m/Y H:i') }} | Elite Locadora</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('accept_terms').addEventListener('change', function() {
            document.getElementById('signBtn').disabled = !this.checked;
        });
    </script>
</body>
</html>
