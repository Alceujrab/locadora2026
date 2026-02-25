@extends('public.layouts.app')

@section('title', 'Assinatura de Contrato - ' . $contract->contract_number)

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Assinatura Digital</h1>
            <p class="text-gray-500 mt-2">Contrato <span class="font-bold text-primary-600">{{ $contract->contract_number }}</span></p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            {{-- Cabeçalho --}}
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Termos do Contrato</h2>
                    <p class="text-sm text-gray-500">Locatário: {{ $contract->customer->name }}</p>
                </div>
                <a href="{{ Storage::disk('public')->url($contract->pdf_path) }}" target="_blank"
                   class="flex items-center gap-2 text-sm font-bold text-primary-600 hover:text-primary-700">
                    📄 Baixar PDF
                </a>
            </div>

            {{-- Visualizador de PDF --}}
            <div class="w-full h-[500px] bg-gray-100 relative">
                <iframe src="{{ Storage::disk('public')->url($contract->pdf_path) }}#toolbar=0"
                        class="w-full h-full border-0 absolute inset-0"></iframe>
            </div>

            {{-- Dados do Contrato --}}
            <div class="p-6 bg-white border-t border-gray-100">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div><span class="text-gray-500">Veículo:</span> <strong>{{ $contract->vehicle?->plate }} - {{ $contract->vehicle?->brand }} {{ $contract->vehicle?->model }}</strong></div>
                    <div><span class="text-gray-500">Período:</span> <strong>{{ $contract->pickup_date?->format('d/m/Y') }} a {{ $contract->return_date?->format('d/m/Y') }}</strong></div>
                    <div><span class="text-gray-500">Total:</span> <strong class="text-green-600">R$ {{ number_format((float)$contract->total, 2, ',', '.') }}</strong></div>
                    <div><span class="text-gray-500">Status:</span> <strong>{{ $contract->status?->value ?? $contract->status }}</strong></div>
                </div>
            </div>

            @if(!$contract->isSigned())
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <form action="{{ route('contract.signature.sign', $contract->id) }}" method="POST" id="signatureForm">
                    @csrf
                    <input type="hidden" name="signature_token" value="{{ $contract->signature_token }}">
                    <input type="hidden" name="signature_data" id="signatureData">
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    {{-- Assinatura manuscrita --}}
                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-3">✍️ Sua Assinatura</h3>
                        <p class="text-sm text-gray-500 mb-3">Desenhe sua assinatura no campo abaixo usando o dedo ou mouse:</p>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg bg-white relative" style="touch-action: none;">
                            <canvas id="signatureCanvas" width="700" height="200"
                                    class="w-full rounded-lg cursor-crosshair" style="max-width:100%;height:200px;"></canvas>
                        </div>
                        <div class="flex justify-end mt-2">
                            <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                🗑 Limpar assinatura
                            </button>
                        </div>
                    </div>

                    {{-- Checkbox aceite --}}
                    <div class="flex items-start mb-6">
                        <div class="flex items-center h-5">
                            <input id="accept_terms" name="accept_terms" type="checkbox" required
                                   class="w-5 h-5 border border-gray-300 rounded bg-white text-primary-600 focus:ring-3 focus:ring-primary-300">
                        </div>
                        <label for="accept_terms" class="ml-3 text-sm font-medium text-gray-900">
                            Li e concordo expressamente com todos os termos e condições do Contrato de Locação nº {{ $contract->contract_number }}.
                        </label>
                    </div>

                    <button type="submit" id="submitBtn" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-bold rounded-lg text-lg px-5 py-4 text-center transition-all shadow-md">
                        ✅ Assinar Contrato Digitalmente
                    </button>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Serão capturados: IP (<span class="font-mono">{{ request()->ip() }}</span>), localização GPS e data/hora para validade jurídica.
                    </p>
                </form>
            </div>
            @endif
        </div>

        @if($contract->isSigned())
        <div class="bg-green-50 rounded-xl p-8 text-center border border-green-200">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-green-800 mb-2">Contrato Assinado! ✅</h2>
            <p class="text-green-700 mb-2">Assinado em {{ $contract->signed_at->format('d/m/Y \à\s H:i') }}</p>
            <p class="text-green-700 mb-2">IP: {{ $contract->signature_ip }}</p>
            @if($contract->signature_latitude)
                <p class="text-green-700 mb-2">Localização: {{ $contract->signature_latitude }}, {{ $contract->signature_longitude }}</p>
            @endif
            <p class="text-xs font-mono text-green-600 bg-green-200/50 p-3 rounded text-left break-all mt-3">
                Hash: {{ $contract->signature_hash }}
            </p>
            @if($contract->signature_image)
                <div class="mt-4">
                    <p class="text-sm text-green-700 mb-2">Assinatura registrada:</p>
                    <img src="{{ Storage::disk('public')->url($contract->signature_image) }}" alt="Assinatura" class="max-w-xs mx-auto border rounded bg-white p-2">
                </div>
            @endif
            <div class="mt-6">
                <a href="{{ Storage::disk('public')->url($contract->pdf_path) }}" target="_blank"
                   class="inline-flex items-center gap-2 text-sm font-bold text-white bg-green-600 py-3 px-6 rounded-lg hover:bg-green-700 transition">
                    📄 Baixar Contrato Assinado
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
// Signature Pad
const canvas = document.getElementById('signatureCanvas');
if (canvas) {
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let lastX = 0, lastY = 0;

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        if (e.touches) {
            return { x: (e.touches[0].clientX - rect.left) * scaleX, y: (e.touches[0].clientY - rect.top) * scaleY };
        }
        return { x: (e.clientX - rect.left) * scaleX, y: (e.clientY - rect.top) * scaleY };
    }

    function startDraw(e) { e.preventDefault(); drawing = true; const p = getPos(e); lastX = p.x; lastY = p.y; }
    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        const p = getPos(e);
        ctx.beginPath(); ctx.moveTo(lastX, lastY); ctx.lineTo(p.x, p.y);
        ctx.strokeStyle = '#1a1a2e'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.stroke();
        lastX = p.x; lastY = p.y;
    }
    function stopDraw() { drawing = false; }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDraw);
    canvas.addEventListener('mouseleave', stopDraw);
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', stopDraw);

    // Geolocalização
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('latitude').value = pos.coords.latitude;
            document.getElementById('longitude').value = pos.coords.longitude;
        }, function() {}, { enableHighAccuracy: true });
    }

    // Submit
    document.getElementById('signatureForm').addEventListener('submit', function(e) {
        const imageData = canvas.toDataURL('image/png');
        // Check if signature is blank
        const blankCanvas = document.createElement('canvas');
        blankCanvas.width = canvas.width; blankCanvas.height = canvas.height;
        if (canvas.toDataURL() === blankCanvas.toDataURL()) {
            e.preventDefault(); alert('Por favor, desenhe sua assinatura antes de enviar.'); return;
        }
        document.getElementById('signatureData').value = imageData;
    });
}

function clearSignature() {
    const c = document.getElementById('signatureCanvas');
    if (c) { c.getContext('2d').clearRect(0, 0, c.width, c.height); }
}
</script>
@endsection
