@extends('public.layouts.app')

@section('title', 'Contrato Assinado - ' . $contract->contract_number)

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="bg-green-50 rounded-xl p-8 text-center border border-green-200 shadow-lg">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-green-800 mb-3">Contrato Assinado! ✅</h1>
            <p class="text-green-700 text-lg mb-6">O contrato <strong>{{ $contract->contract_number }}</strong> foi assinado digitalmente com sucesso.</p>

            <div class="bg-white rounded-lg p-6 text-left space-y-3 mb-6">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Cliente:</span>
                    <strong>{{ $contract->customer?->name }}</strong>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Veículo:</span>
                    <strong>{{ $contract->vehicle?->plate }} - {{ $contract->vehicle?->brand }} {{ $contract->vehicle?->model }}</strong>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Assinado em:</span>
                    <strong>{{ $contract->signed_at?->format('d/m/Y \à\s H:i') }}</strong>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">IP:</span>
                    <span class="font-mono text-xs">{{ $contract->signature_ip }}</span>
                </div>
                @if($contract->signature_latitude)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Localização:</span>
                    <span class="font-mono text-xs">{{ $contract->signature_latitude }}, {{ $contract->signature_longitude }}</span>
                </div>
                @endif
                <div class="pt-2 border-t">
                    <span class="text-gray-500 text-xs">Hash SHA-256:</span>
                    <p class="font-mono text-xs text-gray-600 break-all mt-1">{{ $contract->signature_hash }}</p>
                </div>
            </div>

            @if($contract->signature_image)
            <div class="mb-6">
                <p class="text-sm text-green-700 mb-2">Assinatura registrada:</p>
                <img src="{{ Storage::disk('public')->url($contract->signature_image) }}" alt="Assinatura" class="max-w-xs mx-auto border rounded bg-white p-2">
            </div>
            @endif

            @if($contract->pdf_path)
            <a href="{{ Storage::disk('public')->url($contract->pdf_path) }}" target="_blank"
               class="inline-flex items-center gap-2 text-white bg-green-600 py-3 px-8 rounded-lg hover:bg-green-700 transition font-bold text-lg shadow">
                📄 Baixar Contrato Assinado (PDF)
            </a>
            @endif
        </div>

    </div>
</div>
@endsection
