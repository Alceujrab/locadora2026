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

        /* Canvas de assinatura */
        .signature-pad-wrapper { border: 2px dashed #d1d5db; border-radius: 12px; padding: 8px; margin: 16px 0; background: #fafafa; position: relative; }
        .signature-pad-wrapper.active { border-color: #e67e22; background: #fff; }
        .signature-pad-label { text-align: center; font-size: 12px; color: #9ca3af; margin-bottom: 8px; }
        #signatureCanvas { display: block; width: 100%; height: 200px; border-radius: 8px; cursor: crosshair; touch-action: none; background: #fff; }
        .canvas-actions { display: flex; gap: 8px; margin-top: 8px; }
        .canvas-actions button { flex: 1; padding: 8px; border: 1px solid #d1d5db; background: #fff; border-radius: 6px; font-size: 13px; cursor: pointer; transition: 0.2s; }
        .canvas-actions button:hover { background: #f3f4f6; }
        .canvas-actions .clear-btn { color: #dc2626; border-color: #fca5a5; }
        .canvas-actions .clear-btn:hover { background: #fee2e2; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>ORDEM DE SERVICO #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p>Elite Locadora</p>
            </div>

            <div class="content">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
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

                {{-- Evidencias (Fotos e Videos) --}}
                @if($order->attachments && count($order->attachments) > 0)
                <div class="info-group">
                    <h3>Evidencias do Problema</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; margin-top: 8px;">
                        @foreach($order->attachments as $attachment)
                            @php
                                $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
                                $isVideo = in_array($ext, ['mp4', 'mov', 'avi', 'webm']);
                                $url = asset('storage/' . $attachment);
                            @endphp
                            @if($isVideo)
                                <a href="{{ $url }}" target="_blank" style="display: block; text-decoration: none; border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden; background: #111827; position: relative;">
                                    <div style="padding: 30px 10px; text-align: center;">
                                        <div style="font-size: 36px;">🎬</div>
                                        <p style="color: #9ca3af; font-size: 11px; margin-top: 6px;">Tocar Video</p>
                                        <p style="color: #6b7280; font-size: 10px; margin-top: 2px;">.{{ $ext }}</p>
                                    </div>
                                </a>
                            @else
                                <a href="{{ $url }}" target="_blank" style="display: block; border: 2px solid #e5e7eb; border-radius: 10px; overflow: hidden;">
                                    <img src="{{ $url }}" alt="Evidencia" style="width: 100%; height: 120px; object-fit: cover;">
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif

                <a href="{{ route('os.signature.pdf', $order->id) }}" class="pdf-btn" target="_blank">Baixar PDF da OS</a>

                {{-- Assinatura com Canvas --}}
                <div class="sign-form">
                    <h3>Assinatura Digital</h3>
                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">
                        Desenhe sua assinatura no campo abaixo usando o dedo ou mouse.
                    </p>

                    <form method="POST" action="{{ route('os.signature.sign', $order->id) }}" id="signForm">
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
                                Li e concordo com os termos desta Ordem de Servico. Estou ciente dos servicos descritos acima.
                            </label>
                        </div>

                        <button type="submit" class="sign-btn" id="signBtn" disabled>
                            Assinar Digitalmente
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

        let isDrawing = false;
        let hasDrawn = false;

        // Set canvas resolution
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
            return {
                x: touch.clientX - rect.left,
                y: touch.clientY - rect.top
            };
        }

        function startDraw(e) {
            e.preventDefault();
            isDrawing = true;
            hasDrawn = true;
            wrapper.classList.add('active');
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getPos(e);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function stopDraw(e) {
            if (isDrawing) {
                isDrawing = false;
                ctx.closePath();
                updateBtn();
            }
        }

        // Mouse
        canvas.addEventListener('mousedown', startDraw);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDraw);
        canvas.addEventListener('mouseleave', stopDraw);
        // Touch
        canvas.addEventListener('touchstart', startDraw, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        canvas.addEventListener('touchend', stopDraw);

        function updateBtn() {
            signBtn.disabled = !(hasDrawn && acceptTerms.checked);
        }

        acceptTerms.addEventListener('change', updateBtn);

        window.clearPad = function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
            wrapper.classList.remove('active');
            updateBtn();
        };

        signForm.addEventListener('submit', function(e) {
            if (!hasDrawn) {
                e.preventDefault();
                alert('Desenhe sua assinatura antes de enviar.');
                return false;
            }
            // Export canvas as base64 PNG
            signatureData.value = canvas.toDataURL('image/png');
        });
    })();
    </script>
</body>
</html>
