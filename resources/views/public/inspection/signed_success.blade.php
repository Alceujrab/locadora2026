@extends('public.layouts.app')

@section('title', 'Vistoria Assinada - #' . $inspection->id)

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-green-50 rounded-xl p-8 text-center border border-green-200">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h1 class="text-2xl font-bold text-green-800 mb-2">Vistoria Assinada com Sucesso</h1>
            <p class="text-green-700 text-lg mb-2">A vistoria <strong>#{{ $inspection->id }}</strong> foi assinada digitalmente.</p>
            <p class="text-green-700 mb-2">Assinada em {{ $inspection->signed_at?->format('d/m/Y \à\s H:i') }}</p>
            <p class="text-green-700 mb-2">IP: {{ $inspection->signature_ip }}</p>
            @if($inspection->signature_latitude)
                <p class="text-green-700 mb-2">Localizacao: {{ $inspection->signature_latitude }}, {{ $inspection->signature_longitude }}</p>
            @endif
            <p class="text-xs font-mono text-green-600 bg-green-200/50 p-3 rounded text-left break-all mt-3">Hash: {{ $inspection->signature_hash }}</p>
            @if($inspection->signature_image)
                <div class="mt-4">
                    <p class="text-sm text-green-700 mb-2">Assinatura registrada:</p>
                    <img src="{{ Storage::disk('public')->url($inspection->signature_image) }}" alt="Assinatura" class="max-w-xs mx-auto border rounded bg-white p-2">
                </div>
            @endif
            <div class="mt-6">
                <a href="{{ route('inspection.signature.pdf', $inspection->id) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-green-600 py-3 px-6 rounded-lg hover:bg-green-700 transition">📄 Baixar Vistoria Assinada</a>
            </div>
        </div>
    </div>
</div>
@endsection