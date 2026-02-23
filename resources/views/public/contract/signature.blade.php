@extends('public.layouts.app')

@section('title', 'Assinatura de Contrato - ' . $contract->contract_number)

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Assinatura Digital</h1>
            <p class="text-gray-500 mt-2">Leia os termos abaixo e confirme o aceite digital do contrato <span class="font-bold text-primary-600">{{ $contract->contract_number }}</span></p>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Termos do Contrato</h2>
                    <p class="text-sm text-gray-500">Locatário: {{ $contract->customer->name }}</p>
                </div>
                <a href="{{ Storage::disk('public')->url($contract->pdf_path) }}" target="_blank" class="flex items-center gap-2 text-sm font-bold text-primary-600 hover:text-primary-700">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Baixar PDF
                </a>
            </div>
            
            <!-- Visuzalizador de PDF -->
            <div class="w-full h-[600px] bg-gray-100 relative">
                <iframe src="{{ Storage::disk('public')->url($contract->pdf_path) }}#toolbar=0" class="w-full h-full border-0 absolute inset-0"></iframe>
            </div>

            @if(!$contract->isSigned())
            <div class="p-6 bg-gray-50 border-t border-gray-100">
                <form action="{{ route('contract.signature.sign', $contract->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="signature_token" value="{{ $contract->signature_token }}">
                    
                    <div class="flex items-start mb-6">
                        <div class="flex items-center h-5">
                            <input id="accept_terms" name="accept_terms" type="checkbox" required class="w-5 h-5 border border-gray-300 rounded bg-white text-primary-600 focus:ring-3 focus:ring-primary-300 transition">
                        </div>
                        <label for="accept_terms" class="ml-3 text-sm font-medium text-gray-900">
                            Li e concordo expressamente com todos os termos e condições descritos no Contrato de Locação, possuindo pleno poder e capacidade legal para celebrar a avença.
                        </label>
                    </div>

                    <button type="submit" class="w-full text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-bold rounded-lg text-lg px-5 py-4 text-center transition-all shadow-md">
                        Assinar Eletronicamente e Concordar
                    </button>
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        Ao clicar no botão acima, será capturado seu IP (<span class="font-mono">{{ request()->ip() }}</span>) e a data atual para garantir a validade jurídica da assinatura digital.
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
            <h2 class="text-2xl font-bold text-green-800 mb-2">Contrato Válido e Assinado!</h2>
            <p class="text-green-700 mb-6">Assinado em {{ $contract->signed_at->format('d/m/Y \à\s H:i') }} - IP: {{ $contract->signature_ip }}</p>
            <p class="text-xs font-mono text-green-600 bg-green-200/50 p-3 rounded text-left break-all">
                Hash: {{ $contract->signature_hash }}
            </p>
            <div class="mt-6">
                 <a href="{{ Storage::disk('public')->url($contract->pdf_path) }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-green-600 py-3 px-6 rounded-lg hover:bg-green-700 transition">
                    Ver Cópia do Contrato
                 </a>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
