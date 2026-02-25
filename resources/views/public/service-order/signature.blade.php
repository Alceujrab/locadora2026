<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $signatureType === 'authorization' ? 'Autorizar' : 'Aprovar' }} OS #{{ $order->id }} - Elite Locadora</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; color: #1f2937; min-height: 100vh; }
        .container { max-width: 640px; margin: 0 auto; padding: 20px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; }
        .header { color: #fff; padding: 24px; text-align: center; }
        .header-auth { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .header-approval { background: linear-gradient(135deg, #16a34a, #15803d); }
        .header h1 { font-size: 20px; margin-bottom: 4px; }
        .header p { font-size: 14px; opacity: 0.9; }
        .header .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; margin-top: 8px; }
        .badge-auth { background: rgba(255,255,255,0.25); }
        .badge-approval { background: rgba(255,255,255,0.25); }
        .content { padding: 24px; }
        .info-group { margin-bottom: 16px; }
        .info-group h3 { font-size: 13px; text-transform: uppercase; color: #6b7280; letter-spacing: 0.5px; margin-bottom: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 14px; }
        .info-row .label { color: #6b7280; }
        .info-row .value { font-weight: 600; color: #111827; }
        .description-box { border-left: 4px solid; padding: 12px 16px; border-radius: 0 8px 8px 0; margin: 16px 0; }
        .desc-problem { background: #fef3c7; border-color: #f59e0b; }
        .desc-problem h4 { color: #92400e; }
        .desc-problem p { color: #78350f; }
        .desc-procedure { background: #dbeafe; border-color: #3b82f6; }
        .desc-procedure h4 { color: #1e40af; }
        .desc-procedure p { color: #1e3a5f; }
        .description-box h4 { font-size: 12px; text-transform: uppercase; margin-bottom: 4px; }
        .description-box p { font-size: 14px; }
        .pdf-btn { display: block; width: 100%; padding: 12px; background: #3b82f6; color: #fff; text-align: center; border-radius: 8px; text-decoration: none; font-weight: 600; margin: 16px 0; transition: background 0.2s; }
        .pdf-btn:hover { background: #2563eb; }
        .sign-form { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .sign-form h3 { font-size: 16px; margin-bottom: 6px; color: #111827; }
        .sign-form .subtitle { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .checkbox-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; }
        .checkbox-row input[type="checkbox"] { margin-top: 3px; width: 18px; height: 18px; }
        .checkbox-row label { font-size: 13px; color: #4b5563; line-height: 1.5; }
        .sign-btn { display: block; width: 100%; padding: 14px; color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .sign-btn-auth { background: #2563eb; }
        .sign-btn-auth:hover { background: #1d4ed8; }
        .sign-btn-approval { background: #16a34a; }
        .sign-btn-approval:hover { background: #15803d; }
        .sign-btn:disabled { background: #9ca3af; cursor: not-allowed; }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .footer { text-align: center; padding: 16px; font-size: 11px; color: #9ca3af; }
        .signature-pad-wrapper { border: 2px dashed #d1d5db; border-radius: 12px; padding: 8px; margin: 16px 0; background: #fafafa; position: relative; }
        .signature-pad-wrapper.active { border-color: #3b82f6; background: #fff; }
        .signature-pad-label { text-align: center; font-size: 12px; color: #9ca3af; margin-bottom: 8px; }
        #signatureCanvas { display: block; width: 100%; height: 200px; border-radius: 8px; cursor: crosshair; touch-action: none; background: #fff; }
        .canvas-actions { display: flex; gap: 8px; margin-top: 8px; }
        .canvas-actions button { flex: 1; padding: 8px; border: 1px solid #d1d5db; background: #fff; border-radius: 6px; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .canvas-actions button:hover { background: #f3f4f6; }
        .canvas-actions .clear-btn { color: #dc2626; border-color: #fca5a5; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-top: 8px; }
        .gallery a { display: block; border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
        .gallery img { width: 100%; height: 120px; object-fit: cover; }
        .video-card { text-decoration: none; background: #111827; text-align: center; padding: 30px 10px; }
        .video-card .icon { font-size: 36px; }
        .video-card p { color: #9ca3af; font-size: 11px; margin-top: 6px; }
        .charge-highlight { background: #fef2f2; border: 2px solid #fca5a5; border-radius: 10px; padding: 16px; margin: 16px 0; text-align: center; }
        .charge-highlight .amount { font-size: 28px; font-weight: 800; color: #dc2626; }
        .charge-highlight .label { font-size: 12px; color: #991b1b; text-transform: uppercase; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            {{-- Header diferente por tipo --}}
            <div class="header {{ $signatureType === 'authorization' ? 'header-auth' : 'header-approval' }}">
                <h1>ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p>Elite Locadora</p>
                <span class="badge {{ $signatureType === 'authorization' ? 'badge-auth' : 'badge-approval' }}">
                    {{ $signatureType === 'authorization' ? 'AUTORIZACAO DE ABERTURA' : 'APROVACAO DE CONCLUSAO' }}
                </span>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                {{-- Dados do Veiculo --}}
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

                <div class="description-box desc-problem">
                    <h4>Descricao do Problema</h4>
                    <p>{{ $order->description }}</p>
                </div>

                @if($order->procedure_adopted)
                <div class="description-box desc-procedure">
                    <h4>Procedimento Adotado</h4>
                    <p>{{ $order->procedure_adopted }}</p>
                </div>
                @endif

                {{-- Itens (mostrar sempre na aprovação, opcional na autorização) --}}
                @if($order->items->count() > 0)
                <div class="info-group">
                    <h3>Itens / Servicos Realizados</h3>
                    @foreach($order->items as $item)
                    <div class="info-row">
                        <span class="label">{{ $item->description }} (x{{ $item->quantity }})</span>
                        <span class="value">R$ {{ number_format($item->total, 2, ',', '.') }}</span>
                    </div>
                    @endforeach
                    <div class="info-row" style="border-top: 2px solid #e5e7eb; padding-top: 8px; margin-top: 8px;">
                        <span class="label" style="font-weight:700; color:#111;">TOTAL OS</span>
                        <span class="value" style="font-size: 16px; color: #e67e22;">R$ {{ number_format($order->total, 2, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                {{-- Valor cobrado do cliente (só na aprovação) --}}
                @if($signatureType === 'completion' && $order->customer_charge > 0)
                <div class="charge-highlight">
                    <div class="label">Valor a ser cobrado</div>
                    <div class="amount">R$ {{ number_format($order->customer_charge, 2, ',', '.') }}</div>
                </div>
                @endif

                {{-- Evidências --}}
                @if($order->attachments && count($order->attachments) > 0)
                <div class="info-group">
                    <h3>Evidencias</h3>
                    <div class="gallery">
                        @foreach($order->attachments as $attachment)
                            @php
                                $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                                $url = asset('storage/' . $attachment);
                            @endphp
                            @if($isVideo)
                                <a href="{{ $url }}" target="_blank" style="text-decoration:none;">
                                    <div class="video-card">
                                        <div class="icon">🎬</div>
                                        <p>Tocar Video (.{{ $ext }})</p>
                                    </div>
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank">
                                    <img src="{{ $url }}" alt="Evidencia">
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                @if($order->pdf_path)
                <a href="{{ route('os.signature.pdf', $order->id) }}" class="pdf-btn" target="_blank">Baixar PDF da OS</a>
                @endif

                {{-- Formulário de assinatura --}}
                <div class="sign-form">
                    @if($signatureType === 'authorization')
                        <h3>Autorizacao de Abertura</h3>
                        <p class="subtitle">Ao assinar, voce AUTORIZA a abertura desta Ordem de Servico e o inicio dos reparos no veiculo.</p>
                    @else
                        <h3>Aprovacao dos Servicos</h3>
                        <p class="subtitle">Ao assinar, voce confirma que esta ciente de todos os servicos realizados e aprova a conclusao desta OS.</p>
                    @endif

                    <form method="POST"
                          action="{{ $signatureType === 'authorization' ? route('os.signature.authorize', $order->id) : route('os.signature.approve', $order->id) }}"
                          id="signForm">
                        @csrf
                        <input type="hidden" name="signature_data" id="signatureData">

                        <div class="signature-pad-wrapper" id="padWrapper">
                            <div class="signature-pad-label">Desenhe sua assinatura aqui</div>
                            <canvas id="signatureCanvas"></canvas>
                            <div class="canvas-actions">
                                <button type="button" class="clear-btn" onclick="clearPad()">Limpar</button>
                            </div>
                        </div>

                        @error('signature_data')
                            <div class="alert alert-error">Voce precisa desenhar sua assinatura.</div>
                        @enderror

                        <div class="checkbox-row">
                            <input type="checkbox" name="accept_terms" id="accept_terms" required>
                            <label for="accept_terms">
                                @if($signatureType === 'authorization')
                                    Autorizo a abertura desta Ordem de Servico e estou ciente do problema descrito e do procedimento a ser adotado no veiculo placa <strong>{{ $order->vehicle?->plate }}</strong>.
                                @else
                                    Aprovo os servicos realizados nesta Ordem de Servico e estou ciente dos valores descritos acima.
                                    @if($order->customer_charge > 0)
                                        Concordo com a cobranca de <strong>R$ {{ number_format($order->customer_charge, 2, ',', '.') }}</strong>.
                                    @endif
                                @endif
                            </label>
                        </div>

                        <button type="submit" class="sign-btn {{ $signatureType === 'authorization' ? 'sign-btn-auth' : 'sign-btn-approval' }}" id="signBtn" disabled>
                            {{ $signatureType === 'authorization' ? 'Autorizar Abertura' : 'Aprovar Servicos' }}
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
    (function() {
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        const wrapper = document.getElementById('padWrapper');
        const signForm = document.getElementById('signForm');
        const signatureData = document.getElementById('signatureData');
        const acceptTerms = document.getElementById('accept_terms');
        const signBtn = document.getElementById('signBtn');
        let isDrawing = false, hasDrawn = false;

        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width * 2;
            canvas.height = rect.height * 2;
            ctx.scale(2, 2);
            ctx.strokeStyle = '#1a1a2e';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const touch = e.touches ? e.touches[0] : e;
            return { x: touch.clientX - rect.left, y: touch.clientY - rect.top };
        }
        function startDraw(e) { e.preventDefault(); isDrawing = true; hasDrawn = true; wrapper.classList.add('active'); const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }
        function draw(e) { if (!isDrawing) return; e.preventDefault(); const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); }
        function stopDraw() { if (isDrawing) { isDrawing = false; ctx.closePath(); updateBtn(); } }

        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        function updateBtn() { signBtn.disabled = !(hasDrawn && acceptTerms.checked); }
        acceptTerms.addEventListener('change', updateBtn);

        window.clearPad = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false; wrapper.classList.remove('active'); updateBtn();
        };

        signForm.addEventListener('submit', function(e) {
            if (!hasDrawn) { e.preventDefault(); alert('Desenhe sua assinatura.'); return false; }
            signatureData.value = canvas.toDataURL('image/png');
        });
    })();
    </script>
</body>
</html>
