@extends('client.layouts.app')
@section('title', 'Início')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold font-display text-slate-900">Olá, {{ explode(' ', $customer->name)[0] }}!</h2>
    <p class="mt-1 text-slate-500">Bem-vindo(a) ao seu painel exclusivo Locadora 2026.</p>
</div>

<!-- Resumo Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 lg:gap-8 mb-10">
    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="p-4 bg-purple-50 rounded-xl">
            <svg class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Contratos Ativos</p>
            <p class="text-2xl font-bold font-display text-slate-900">{{ $activeContractsCount }}</p>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="p-4 bg-red-50 rounded-xl">
            <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Faturas Pendentes</p>
            <p class="text-2xl font-bold font-display text-slate-900">{{ $pendingInvoicesCount }}</p>
        </div>
    </div>

    <div class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="p-4 bg-orange-50 rounded-xl">
            <svg class="w-8 h-8 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.436 3 12c0 1.967.653 3.765 1.761 5.23l-1.071 2.946a.75.75 0 00.941.986l3.051-1.077A9.043 9.043 0 0012 20.25z" />
            </svg>
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">Chamados Abertos</p>
            <p class="text-2xl font-bold font-display text-slate-900">{{ $openTicketsCount }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Faturas Section -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold font-display text-slate-900">Suas Faturas e Avisos</h3>
            <a href="{{ route('cliente.invoices') }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">Ver todas</a>
        </div>
        
        @if($pendingInvoicesCount > 0)
            <ul role="list" class="divide-y divide-slate-100">
                @foreach($customer->invoices()->where('status', 'open')->orderBy('due_date', 'asc')->take(4)->get() as $invoice)
                <li class="flex flex-wrap items-center justify-between gap-x-6 gap-y-4 p-6 sm:flex-nowrap">
                    <div>
                        <p class="text-sm font-medium leading-6 text-slate-900">
                            Fatura #{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}
                            @if($invoice->due_date < now())
                                <span class="inline-flex items-center rounded-md bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10 ml-2">Vencida</span>
                            @else
                                <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/10 ml-2">Pendente</span>
                            @endif
                        </p>
                        <div class="mt-1 flex items-center gap-x-2 text-xs leading-5 text-slate-500">
                            <p>Vencimento: {{ $invoice->due_date->format('d/m/Y') }}</p>
                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                            <p>Contrato #{{ $invoice->contract->contract_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <dl class="flex w-full flex-none justify-between gap-x-8 sm:w-auto">
                        <div class="flex flex-col gap-y-1 sm:items-end">
                            <dt class="text-xs font-medium text-slate-500">Valor</dt>
                            <dd class="text-sm leading-6 font-bold text-slate-900">R$ {{ number_format($invoice->total_with_charges ?? $invoice->total, 2, ',', '.') }}</dd>
                        </div>
                        <a href="{{ route('cliente.invoices') }}" class="rounded-lg bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-100 flex items-center h-fit self-center">
                            Pagar
                        </a>
                    </dl>
                </li>
                @endforeach
            </ul>
        @else
            <div class="p-8 text-center text-slate-500 flex flex-col items-center">
                <div class="bg-green-50 p-3 rounded-full mb-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <p class="font-medium text-slate-700">Tudo em dia!</p>
                <p class="text-sm mt-1">Nenhuma fatura em aberto no momento.</p>
            </div>
        @endif
    </div>

    <!-- Próximas Reservas -->
    <div class="bg-white shadow-sm rounded-2xl border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold font-display text-slate-900">Próximas Reservas</h3>
            <a href="{{ route('cliente.reservations') }}" class="text-sm font-medium text-purple-600 hover:text-purple-500">Ver todas</a>
        </div>
        
        @if($upcomingReservations->count() > 0)
            <ul role="list" class="divide-y divide-slate-100">
                @foreach($upcomingReservations as $reservation)
                <li class="p-6 hover:bg-slate-50/50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200">
                                <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $reservation->vehicle->title ?? 'Veículo Padrão' }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">Retirada: {{ $reservation->pickup_date->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $reservation->status->color() === 'success' ? 'bg-green-50 text-green-700 ring-green-600/10' : 'bg-slate-50 text-slate-700 ring-slate-600/10' }}">
                            {{ $reservation->status->label() }}
                        </span>
                    </div>
                </li>
                @endforeach
            </ul>
        @else
            <div class="p-8 text-center text-slate-500 flex flex-col items-center">
                <div class="bg-slate-50 p-3 rounded-full mb-3">
                    <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="font-medium text-slate-700">Nenhuma reserva agendada</p>
                <a href="{{ route('public.vehicles') }}" class="text-sm mt-2 text-purple-600 hover:text-purple-500 font-medium">+ Fazer nova reserva</a>
            </div>
        @endif
    </div>

</div>
@endsection
