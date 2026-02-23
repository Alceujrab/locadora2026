@extends('client.layouts.app')
@section('title', 'Contratos e Checklists')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-bold font-display text-gray-900">Seus Contratos</h2>
        <p class="mt-2 text-sm text-gray-500 font-medium">Histórico de locações, km inicial/final e status de devolução.</p>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-gray-900 sm:pl-6">Contrato</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Veículo</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Período</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Km Original</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Status</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Opções</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-bold text-gray-900 sm:pl-6">
                        #{{ $contract->contract_number }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        {{ $contract->vehicle->title ?? 'N/A' }}<br>
                        <span class="text-xs text-gray-400">Placa: {{ $contract->vehicle->plate ?? '-' }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($contract->pickup_date)->format('d/m/y H:i') }} <br>
                        <span class="text-xs text-gray-400 font-semibold">até {{ \Carbon\Carbon::parse($contract->return_date)->format('d/m/y H:i') }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-bold">
                        {{ number_format($contract->pickup_mileage, 0, '', '.') }} km
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @if($contract->status === 'draft')
                            <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">Rascunho</span>
                        @elseif($contract->status === 'active' || $contract->status === 'em_andamento')
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">Em Andamento</span>
                        @elseif($contract->status === 'completed' || $contract->status === 'fechado')
                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">Finalizado</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Cancelado</span>
                        @endif
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="#" class="text-primary-600 hover:text-primary-900 font-bold transition-colors flex items-center justify-end gap-1" onclick="alert('Funcionalidade de Download de Via Assinada em desenvolvimento.')">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                            Baixar Cópia
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <p class="font-bold text-gray-900">Sem Contratos</p>
                        <p class="mt-1 font-medium">Você não possui locações ativas ou no histórico.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
