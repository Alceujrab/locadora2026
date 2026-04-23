@extends('client.layouts.app')
@section('title', 'Minhas Multas')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-bold font-display text-gray-900">Multas e Infrações</h2>
        <p class="mt-2 text-sm text-gray-500 font-medium">Acompanhe aqui todas as multas de trânsito associadas aos seus contratos de locação.</p>
    </div>
</div>

@php
    $pending = $fines->filter(fn($f) => in_array($f->status, ['pendente','indicado']))->count();
    $paid = $fines->filter(fn($f) => $f->status === 'pago')->count();
    $total = $fines->sum('amount');
@endphp

<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pendentes</p>
        <p class="mt-2 font-display text-3xl font-black text-amber-600">{{ $pending }}</p>
    </div>
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pagas / Finalizadas</p>
        <p class="mt-2 font-display text-3xl font-black text-emerald-600">{{ $paid }}</p>
    </div>
    <div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl p-5">
        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total em multas</p>
        <p class="mt-2 font-display text-3xl font-black text-gray-900">R$ {{ number_format($total, 2, ',', '.') }}</p>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-gray-900 sm:pl-6">AIT / Código</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Veículo</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Data Infração</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Vencimento</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Valor</th>
                <th class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($fines as $fine)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-bold text-gray-900 sm:pl-6">
                        {{ $fine->auto_infraction_number ?? '—' }}
                        @if($fine->fine_code)
                            <div class="text-xs font-normal text-gray-400">Código {{ $fine->fine_code }}</div>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        @if($fine->vehicle)
                            {{ $fine->vehicle->license_plate ?? '' }}
                            <div class="text-xs text-gray-400">{{ $fine->vehicle->brand }} {{ $fine->vehicle->model }}</div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        {{ $fine->fine_date ? \Carbon\Carbon::parse($fine->fine_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                        {{ $fine->due_date ? \Carbon\Carbon::parse($fine->due_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm font-bold text-gray-900">
                        R$ {{ number_format($fine->amount ?? 0, 2, ',', '.') }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        @php
                            $statusMap = [
                                'pendente' => ['bg-amber-50','text-amber-700','ring-amber-600/20','Pendente'],
                                'indicado' => ['bg-blue-50','text-blue-700','ring-blue-600/20','Indicado'],
                                'pago' => ['bg-emerald-50','text-emerald-700','ring-emerald-600/20','Pago'],
                                'recorrido' => ['bg-purple-50','text-purple-700','ring-purple-600/20','Em recurso'],
                                'cancelado' => ['bg-gray-50','text-gray-600','ring-gray-500/10','Cancelado'],
                            ];
                            $s = $statusMap[$fine->status] ?? ['bg-gray-50','text-gray-700','ring-gray-500/10', ucfirst($fine->status ?? '—')];
                        @endphp
                        <span class="inline-flex items-center rounded-md {{ $s[0] }} px-2 py-1 text-xs font-medium {{ $s[1] }} ring-1 ring-inset {{ $s[2] }}">{{ $s[3] }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Você está em dia! Nenhuma multa registrada.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
