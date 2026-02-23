@extends('public.layouts.app')

@section('title', 'Confirmação - Passo 3')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Bar -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Quase lá, {{ explode(' ', $customer->name)[0] }}!</h1>
            <p class="text-gray-500 mt-2">Revise os detalhes abaixo para confirmar sua reserva.</p>
            
            <nav aria-label="Progress" class="mt-8 flex justify-center">
                <ol role="list" class="flex items-center">
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-primary-600"></div>
                        </div>
                        <a href="{{ route('checkout.extras', ['vehicle_id' => $reservationData['vehicle_id'], 'start' => $reservationData['start_date'], 'end' => $reservationData['end_date']]) }}" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">1. Opcionais</span>
                    </li>
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-primary-600"></div>
                        </div>
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">2. Identificação</span>
                    </li>
                    <li class="relative">
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600" aria-current="step">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">3. Conclusão</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 overflow-hidden">
            <div class="p-8">
                
                <!-- Dados do Titular -->
                <div class="mb-8 flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold text-xl flex-shrink-0">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Titular da Reserva</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $customer->name }}</p>
                        <p class="text-sm text-gray-500">{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $customer->cpf_cnpj) }} &bull; {{ $customer->email }}</p>
                    </div>
                </div>

                <!-- Detalhes do Veículo e Período -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8 border-b border-gray-100 pb-8">
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Veículo Selecionado</h4>
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-14 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-200">
                                @if($vehicle->photos && count($vehicle->photos) > 0)
                                    <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-[9px] font-bold text-gray-400 uppercase">Sem Foto</span>
                                @endif
                            </div>
                            <div>
                                <p class="text-lg font-bold text-gray-900">{{ $vehicle->model }}</p>
                                <p class="text-sm text-gray-500">{{ $vehicle->category->name ?? 'Categoria' }} &bull; {{ $vehicle->transmission ?? 'Manual' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded p-4 flex justify-between items-center border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Retirada</p>
                            <p class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($reservationData['start_date'])->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $vehicle->branch->name ?? 'Loja Principal' }}</p>
                        </div>
                        <div class="text-gray-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Devolução</p>
                            <p class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($reservationData['end_date'])->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $vehicle->branch->name ?? 'Loja Principal' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Resumo Financeiro -->
                <div class="mb-8">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Resumo Financeiro</h4>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">{{ $reservationData['days'] }}x Diárias veículo (R$ {{ number_format($reservationData['daily_rate'], 2, ',', '.') }})</span>
                            <span class="font-bold text-gray-900">R$ {{ number_format($reservationData['vehicle_total'], 2, ',', '.') }}</span>
                        </div>
                        
                        @if(isset($reservationData['selected_extras']) && count($reservationData['selected_extras']) > 0)
                            <div class="pt-2 border-t border-gray-100 space-y-3">
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Proteções e Extras</p>
                                @foreach($reservationData['selected_extras'] as $extra)
                                    <div class="flex justify-between items-center text-primary-700">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span class="font-medium">{{ $extra['name'] }}</span>
                                        </div>
                                        <span class="font-bold">R$ {{ number_format($extra['total'], 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Valor Total da Reserva</p>
                        <p class="text-xs text-gray-500 mt-1">Caução de R$ 1.000,00 será retida no cartão de crédito na retirada do veículo.</p>
                    </div>
                    <div class="text-right">
                        <span class="text-3xl font-extrabold text-primary-600 tracking-tight">R$ {{ number_format($reservationData['grand_total'], 2, ',', '.') }}</span>
                    </div>
                </div>

                <form action="{{ route('checkout.finish') }}" method="POST">
                    @csrf
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('checkout.extras', ['vehicle_id' => $reservationData['vehicle_id'], 'start' => $reservationData['start_date'], 'end' => $reservationData['end_date']]) }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 transition">
                            Voltar e Editar
                        </a>
                        <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg shadow-md hover:bg-primary-700 hover:-translate-y-0.5 transition-all focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 flex items-center gap-2">
                            Aprovar e Solicitar Reserva
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
        
    </div>
</div>
@endsection
