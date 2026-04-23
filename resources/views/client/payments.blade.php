@extends('client.layouts.app')
@section('title', 'Pagamentos')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-bold font-display text-gray-900">Histórico de Pagamentos</h2>
        <p class="mt-2 text-sm text-gray-500 font-medium">Todos os pagamentos liquidados em suas faturas.</p>
    </div>
</div>

@php
    $total = $payments->where('status', '!=', 'refunded')->sum('amount');
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total pago</p>
        <p class="mt-2 font-display text-3xl font-black text-emerald-600">R$ {{ number_format($total, 2, ',', '.') }}</p>
    </div>
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Qtd. pagamentos</p>
        <p class="mt-2 font-display text-3xl font-black text-gray-900">{{ $payments->count() }}</p>
    </div>
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Último pagamento</p>
        <p class="mt-2 font-display text-lg font-bold text-gray-900">
            {{ $payments->first()?->paid_at ? \Carbon\Carbon::parse($payments->first()->paid_at)->format('d/m/Y') : '—' }}
        </p>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-gray-900 sm:pl-6">Data</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Fatura / Contrato</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Método</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Valor</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($payments as $payment)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-bold text-gray-900 sm:pl-6">
                        {{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        @if($payment->invoice)
                            Fatura #{{ str_pad($payment->invoice->id, 5, '0', STR_PAD_LEFT) }}
                            @if($payment->invoice->contract)
                                <div class="text-xs text-gray-400">Contrato #{{ $payment->invoice->contract->contract_number }}</div>
                            @endif
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        @php
                            $methodLabels = [
                                'pix' => 'PIX',
                                'boleto' => 'Boleto',
                                'credit_card' => 'Cartão de Crédito',
                                'debit_card' => 'Cartão de Débito',
                                'money' => 'Dinheiro',
                                'transfer' => 'Transferência',
                            ];
                            $methodKey = is_object($payment->method) ? $payment->method->value : $payment->method;
                        @endphp
                        {{ $methodLabels[$methodKey] ?? ucfirst($methodKey ?? '—') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900">
                        R$ {{ number_format($payment->amount ?? 0, 2, ',', '.') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($payment->status === 'paid')
                            <span class="inline-flex items-center rounded-md bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">Confirmado</span>
                        @elseif($payment->status === 'refunded')
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-inset ring-purple-600/20">Estornado</span>
                        @elseif($payment->status === 'failed')
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20">Falhou</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-700 ring-1 ring-inset ring-gray-500/10">{{ ucfirst($payment->status ?? '—') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 px-4 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Nenhum pagamento registrado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
