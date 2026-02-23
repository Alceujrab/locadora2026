@extends('public.layouts.app')

@section('title', 'Finalizar Reserva - Passo 1')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Revise sua Reserva</h1>
            <nav aria-label="Progress" class="mt-4">
                <ol role="list" class="flex items-center">
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-primary-600"></div>
                        </div>
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">1. Opcionais</span>
                    </li>
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-gray-200"></div>
                        </div>
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white" aria-current="step">
                            <span class="h-2.5 w-2.5 rounded-full bg-transparent"></span>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-semibold text-gray-500 w-max">2. Identificação</span>
                    </li>
                    <li class="relative">
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                            <span class="h-2.5 w-2.5 rounded-full bg-transparent"></span>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-semibold text-gray-500 w-max">3. Conclusão</span>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 mt-12">
            
            <!-- Left Content: Extras Selection -->
            <main class="w-full lg:w-2/3">
                <form action="{{ route('checkout.process_extras') }}" method="POST" id="extras-form">
                    @csrf
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900">Proteção e Serviços Adicionais</h2>
                            <p class="text-sm text-gray-500 mt-1">Personalize sua locação adicionando itens que garantem mais tranquilidade e conforto.</p>
                        </div>
                        
                        <div class="divide-y divide-gray-100">
                            @forelse($extras as $extra)
                                <div class="px-6 py-5 flex items-start hover:bg-gray-50 transition cursor-pointer" onclick="document.getElementById('extra_{{ $extra->id }}').click()">
                                    <div class="flex items-center h-5 mt-1">
                                        <input id="extra_{{ $extra->id }}" name="extras[]" value="{{ $extra->id }}" type="checkbox" class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-600 cursor-pointer" onclick="event.stopPropagation()" onchange="calculateTotal()">
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="flex justify-between">
                                            <label for="extra_{{ $extra->id }}" class="font-bold text-gray-900 cursor-pointer">{{ $extra->name }}</label>
                                            <span class="text-sm font-bold text-gray-900" data-price="{{ $extra->price }}" data-type="{{ $extra->charge_type }}" id="price_display_{{ $extra->id }}">
                                                + R$ {{ number_format($extra->price, 2, ',', '.') }} {{ $extra->charge_type == 'por_dia' ? '/dia' : 'único' }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">{{ $extra->description ?? 'Serviço adicional para sua locação.' }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum adicional disponível no momento.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="inline-flex items-center justify-center px-8 py-3 h-[46px] border border-transparent text-base font-bold rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition shadow-sm w-full sm:w-auto">
                            Continuar para Identificação
                        </button>
                    </div>
                </form>
            </main>

            <!-- Right Content: Order Summary -->
            <aside class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 sticky top-6 overflow-hidden">
                    <div class="bg-gray-50 p-6 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Resumo da Reserva</h3>
                    </div>
                    
                    <div class="p-6">
                        <!-- Vehicle Brief -->
                        <div class="flex gap-4 mb-6">
                            <div class="w-24 h-16 bg-gray-100 rounded-md overflow-hidden flex-shrink-0">
                                @if($vehicle->photos && count($vehicle->photos) > 0)
                                    <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium uppercase text-center flex items-center justify-center h-full">Foto</span>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $vehicle->model }}</h4>
                                <p class="text-xs text-gray-500">{{ $vehicle->category->name ?? 'Categoria' }}</p>
                            </div>
                        </div>

                        <!-- Dates -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-500 font-medium">Retirada:</span>
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 font-medium">Devolução:</span>
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="space-y-3 text-sm border-b border-gray-100 pb-4 mb-4" id="breakdown-container">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ $days }}x Diárias (R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }})</span>
                                <span class="font-medium text-gray-900">R$ {{ number_format($vehicleTotal, 2, ',', '.') }}</span>
                            </div>
                            <!-- Extras will be injected here by JS -->
                        </div>

                        <div class="flex items-end justify-between pt-2">
                            <span class="text-base font-bold text-gray-900">Total Previsto</span>
                            <span class="text-2xl font-extrabold text-primary-600" id="grand-total">R$ {{ number_format($vehicleTotal, 2, ',', '.') }}</span>
                        </div>
                        <p class="text-xs text-right text-gray-400 mt-1">Taxas inclusas</p>
                    </div>
                </div>
            </aside>
            
        </div>
    </div>
</div>

<script>
    const days = {{ $days }};
    const vehicleTotal = {{ $vehicleTotal }};
    
    function calculateTotal() {
        let extrasTotal = 0;
        const breakdownContainer = document.getElementById('breakdown-container');
        
        // Remove old dynamic extra rows
        document.querySelectorAll('.dynamic-extra').forEach(e => e.remove());
        
        document.querySelectorAll('input[name="extras[]"]:checked').forEach((checkbox) => {
            const extraId = checkbox.value;
            const priceSpan = document.getElementById('price_display_' + extraId);
            const price = parseFloat(priceSpan.getAttribute('data-price'));
            const type = priceSpan.getAttribute('data-type');
            const name = checkbox.closest('div.flex-1').querySelector('label').innerText;
            
            let itemTotal = price;
            if(type === 'por_dia') {
                itemTotal = price * days;
            }
            extrasTotal += itemTotal;
            
            // Add to breakdown visually
            const row = document.createElement('div');
            row.className = 'flex justify-between dynamic-extra text-primary-700';
            row.innerHTML = `<span>+ ${name}</span><span class="font-medium">R$ ${itemTotal.toFixed(2).replace('.', ',')}</span>`;
            breakdownContainer.appendChild(row);
        });
        
        const grandTotal = vehicleTotal + extrasTotal;
        document.getElementById('grand-total').innerText = 'R$ ' + grandTotal.toFixed(2).replace('.', ',');
    }
</script>
@endsection
