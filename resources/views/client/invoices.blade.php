@extends('client.layouts.app')
@section('title', 'Faturas e Pagamentos')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-semibold font-display text-slate-900">Histórico de Faturas</h2>
        <p class="mt-2 text-sm text-slate-500">Confira abaixo todos os seus relatórios financeiros, boletos e chaves PIX de pagamentos.</p>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Número</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Contrato / Veículo</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Vencimento</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Valor Total</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Status</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Ações</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($invoices as $invoice)
                @php
                    $isOverdue = $invoice->status === 'open' && $invoice->due_date < now();
                @endphp
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6">
                        #{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                        @if($invoice->contract)
                            Contrato #{{ $invoice->contract->contract_number }}<br>
                            <span class="text-xs text-slate-400">{{ $invoice->contract->vehicle->title ?? 'N/A' }}</span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm {{ $isOverdue ? 'text-red-600 font-semibold' : 'text-slate-500' }}">
                        {{ $invoice->due_date->format('d/m/Y') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-slate-900">
                        R$ {{ number_format($invoice->total_with_charges ?? $invoice->total, 2, ',', '.') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($invoice->status === 'paid')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Pago</span>
                        @elseif($invoice->status === 'cancelled')
                            <span class="inline-flex items-center rounded-md bg-slate-50 px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-inset ring-slate-500/10">Cancelado</span>
                        @else
                            @if($isOverdue)
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Vencida</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">Aberta</span>
                            @endif
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        @if($invoice->status === 'open')
                            <a href="#" class="text-purple-600 hover:text-purple-900 flex items-center justify-end gap-1" onclick="alert('Funcionalidade de Geração de PIX em desenvolvimento para o Portal Cliente.\\n\\nSolicite o QR Code diretamente ao atendimento.')">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                </svg>
                                Pagar com PIX
                            </a>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center text-sm text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-medium text-slate-900">Nenhuma Fatura</p>
                        <p class="mt-1">Você não possui nenhum histórico de faturas no momento.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
