@extends('client.layouts.app')

@section('title', 'Ordens de Servico')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Ordens de Servico</h2>
            <p class="mt-1 text-sm text-gray-500">Acompanhe as ordens de servico dos seus veiculos</p>
        </div>
    </div>

    {{-- Contadores --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Pendentes</p>
            <p class="text-2xl font-bold text-orange-500 mt-1">{{ $orders->whereIn('status', [\App\Enums\ServiceOrderStatus::OPEN, \App\Enums\ServiceOrderStatus::IN_PROGRESS])->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Aguardando Assinatura</p>
            <p class="text-2xl font-bold text-yellow-500 mt-1">{{ $orders->where('status', \App\Enums\ServiceOrderStatus::AWAITING_SIGNATURE)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Concluidas</p>
            <p class="text-2xl font-bold text-green-500 mt-1">{{ $orders->where('status', \App\Enums\ServiceOrderStatus::COMPLETED)->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total</p>
            <p class="text-2xl font-bold text-gray-700 mt-1">{{ $orders->count() }}</p>
        </div>
    </div>

    {{-- Lista de OS --}}
    @if($orders->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.386 3.075A.478.478 0 015 17.764V3h14v14.764a.478.478 0 01-1.034.481l-5.386-3.075a.478.478 0 00-.478 0z" />
            </svg>
            <h3 class="mt-4 text-sm font-semibold text-gray-900">Nenhuma Ordem de Servico</h3>
            <p class="mt-1 text-sm text-gray-500">Voce nao possui ordens de servico registradas.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-gray-900">OS #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
                                @php
                                    $statusColors = [
                                        'aberta' => 'bg-blue-100 text-blue-700',
                                        'em_andamento' => 'bg-purple-100 text-purple-700',
                                        'aguardando_assinatura' => 'bg-yellow-100 text-yellow-700',
                                        'concluida' => 'bg-green-100 text-green-700',
                                        'fechada' => 'bg-gray-100 text-gray-700',
                                        'cancelada' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$order->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $order->status->label() }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">{{ ucfirst($order->type) }} | {{ $order->opened_at?->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-orange-500">R$ {{ number_format($order->total, 2, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-400 text-xs">Veiculo</span>
                            <p class="font-medium text-gray-700">{{ $order->vehicle?->plate ?? '-' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-400 text-xs">Oficina</span>
                            <p class="font-medium text-gray-700">{{ $order->supplier?->name ?? '-' }}</p>
                        </div>
                    </div>

                    <p class="mt-3 text-sm text-gray-600">{{ Str::limit($order->description, 120) }}</p>

                    {{-- Acoes --}}
                    <div class="mt-4 flex gap-2">
                        @if($order->status === \App\Enums\ServiceOrderStatus::AWAITING_SIGNATURE && !$order->isSigned())
                            <a href="{{ route('os.signature.show', $order->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-xs font-bold text-white hover:bg-orange-600 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                Assinar
                            </a>
                        @endif

                        @if($order->pdf_path)
                            <a href="{{ route('os.signature.pdf', $order->id) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100 transition border border-blue-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                Baixar PDF
                            </a>
                        @endif

                        @if($order->isSigned())
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 border border-green-200">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Assinada em {{ $order->signed_at?->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
