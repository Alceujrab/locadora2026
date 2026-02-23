@extends('public.layouts.app')

@section('title', $vehicle->model . ' - ' . $vehicle->brand)

@section('content')
<!-- Header & Breadcrumbs (Dark) -->
<div class="bg-gray-900 pt-8 pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex text-sm text-gray-400 mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('public.home') }}" class="hover:text-white transition">Início</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('public.vehicles') }}" class="hover:text-white transition">Frota</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-500 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-white font-semibold">{{ $vehicle->model }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary-500/20 text-primary-300 border border-primary-500/30">
                        {{ $vehicle->category->name ?? 'Cat. Padrão' }}
                    </span>
                    <span class="text-gray-400 text-sm font-medium">{{ $vehicle->year }}</span>
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white tracking-tight">{{ $vehicle->model }}</h1>
                <p class="text-xl text-gray-300 mt-2">{{ $vehicle->brand }}</p>
            </div>
            <div class="text-left md:text-right">
                <p class="text-sm text-gray-400 font-medium uppercase tracking-wider mb-1">A partir de</p>
                <div class="flex items-baseline md:justify-end gap-1 text-white">
                    <span class="text-4xl font-black">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                    <span class="text-lg text-gray-400">/dia</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Area -->
<div class="bg-gray-50 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-24 relative z-10">
        
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            <!-- Left Column: Gallery & Details -->
            <main class="w-full lg:w-2/3 space-y-8">
                
                <!-- Image Gallery -->
                <div class="bg-white rounded-2xl p-2 sm:p-4 shadow-xl shadow-gray-200/50">
                    <!-- Main Image -->
                    <div class="relative w-full h-[300px] sm:h-[450px] rounded-xl overflow-hidden bg-gray-100 group">
                        @if($vehicle->photos && count($vehicle->photos) > 0)
                            <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" id="main-gallery-image">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Sem imagens disponíveis</span>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnails -->
                    @if($vehicle->photos && count($vehicle->photos) > 1)
                        <div class="flex gap-2 sm:gap-4 mt-2 sm:mt-4 overflow-x-auto pb-2 custom-scrollbar">
                            @foreach($vehicle->photos as $index => $photo)
                                <button type="button" class="relative flex-none w-20 h-16 sm:w-32 sm:h-24 rounded-lg overflow-hidden border-2 {{ $index === 0 ? 'border-primary-600' : 'border-transparent' }} hover:border-primary-400 transition-colors focus:outline-none" onclick="document.getElementById('main-gallery-image').src='{{ asset('storage/' . $photo) }}'; this.parentNode.querySelectorAll('button').forEach(b => b.classList.replace('border-primary-600', 'border-transparent')); this.classList.replace('border-transparent', 'border-primary-600');">
                                    <span class="sr-only">Ver imagem {{ $index + 1 }}</span>
                                    <img src="{{ asset('storage/' . $photo) }}" alt="" class="absolute inset-0 w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Technical Specs -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Ficha Técnica
                    </h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                        <div class="bg-gray-50/80 p-5 rounded-xl border border-gray-100/50 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-primary-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Câmbio</span>
                            <span class="text-base text-gray-900 font-bold mt-1">{{ $vehicle->transmission ?? 'Manual' }}</span>
                        </div>
                        <div class="bg-gray-50/80 p-5 rounded-xl border border-gray-100/50 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-primary-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Portas</span>
                            <span class="text-base text-gray-900 font-bold mt-1">{{ $vehicle->doors }}</span>
                        </div>
                        <div class="bg-gray-50/80 p-5 rounded-xl border border-gray-100/50 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-primary-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Assentos</span>
                            <span class="text-base text-gray-900 font-bold mt-1">{{ $vehicle->passengers ?? 5 }}</span>
                        </div>
                        <div class="bg-gray-50/80 p-5 rounded-xl border border-gray-100/50 flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-primary-600">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Combustível</span>
                            <span class="text-base text-gray-900 font-bold mt-1">{{ $vehicle->fuel_type }}</span>
                        </div>
                    </div>
                </div>

                <!-- Accessories -->
                @if($vehicle->accessories && count($vehicle->accessories) > 0)
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-3 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Acessórios e Opcionais
                    </h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($vehicle->accessories as $accessory)
                            <li class="flex items-center text-gray-700">
                                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </div>
                                <span class="font-medium text-sm">{{ $accessory }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <!-- Important Info -->
                <div class="bg-blue-50 border border-blue-100/50 rounded-2xl p-6 sm:p-8">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900 mb-2">Informações Importantes</h3>
                            <div class="space-y-2 text-sm text-blue-800/90 leading-relaxed">
                                <p>&bull; <strong>Requisitos:</strong> CNH válida e definitiva. Idade mínima exigida pela categoria.</p>
                                <p>&bull; <strong>Garantia Única:</strong> Será exigido cartão de crédito em nome do locatário com limite para o caução (R$ {{ number_format($vehicle->category->deposit_amount ?? 1000, 2, ',', '.') }}).</p>
                                <p>&bull; <strong>Seguro Inclusos:</strong> Proteção básica do veículo, com participação obrigatória (franquia) em caso de sinistro.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <!-- Right Column: Sticky Reservation Sidebar -->
            <aside class="w-full lg:w-1/3">
                <div class="sticky top-24">
                    <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        
                        <!-- Price Header -->
                        <div class="bg-gray-900 p-6 text-white">
                            <p class="text-sm text-gray-400 font-bold uppercase tracking-wider mb-2">Tarifa Diária</p>
                            <div class="flex items-end gap-2">
                                <span class="text-5xl font-black">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        <!-- Booking Form -->
                        <div class="p-6 sm:p-8">
                            <form action="{{ route('checkout.extras') }}" method="GET" class="space-y-6" x-data="{
                                start: '',
                                end: '',
                                days() {
                                    if(!this.start || !this.end) return 0;
                                    const diff = new Date(this.end) - new Date(this.start);
                                    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
                                },
                                total() {
                                    return this.days() * {{ $vehicle->category->daily_rate ?? 0 }};
                                }
                            }">
                                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">

                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Data e Hora de Retirada</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <input type="date" name="start" x-model="start" min="{{ date('Y-m-d') }}" class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-sm font-medium bg-gray-50/50" required>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">Data e Hora de Devolução</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <input type="date" name="end" x-model="end" :min="start" class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-sm font-medium bg-gray-50/50" required>
                                    </div>
                                </div>
                                
                                <!-- Detailed Calculation Box (alpinejs logic) -->
                                <div class="bg-gray-50 p-5 rounded-xl border border-gray-200/60" x-show="days() > 0" style="display: none;" x-transition>
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Resumo da Locação</h4>
                                    
                                    <div class="flex justify-between text-sm mb-3">
                                        <span class="text-gray-600">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }} x <span x-text="days()"></span> diárias</span>
                                        <span class="font-bold text-gray-900">R$ <span x-text="total().toFixed(2).replace('.', ',')"></span></span>
                                    </div>
                                    <div class="flex justify-between text-sm mb-4">
                                        <span class="text-gray-600">Seguro e Proteção</span>
                                        <span class="font-bold text-green-600">Incluso</span>
                                    </div>
                                    
                                    <div class="border-t border-gray-200 pt-4 mt-2 flex justify-between items-center">
                                        <span class="font-bold text-gray-900">Total Previsto</span>
                                        <span class="text-xl font-black text-primary-600">R$ <span x-text="total().toFixed(2).replace('.', ',')"></span></span>
                                    </div>
                                </div>

                                <button type="submit" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-primary-500/30 text-base font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:-translate-y-1">
                                    Iniciar Reserva
                                </button>
                                
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 font-medium">Nenhuma cobrança será feita agora.</p>
                                    <p class="text-xs text-gray-400 mt-1">O pagamento ocorre no checkout.</p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>
            
        </div>

        <!-- Related Vehicles Bottom -->
        @if(count($relatedVehicles) > 0)
        <div class="mt-24">
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 mb-8 sm:mb-10 text-center sm:text-left">Veículos Semelhantes</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($relatedVehicles as $related)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden flex flex-col">
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            @if($related->photos && count($related->photos) > 0)
                                <img src="{{ asset('storage/' . $related->photos[0]) }}" alt="{{ $related->model }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">Sem Foto</div>
                            @endif
                            <div class="absolute top-3 left-3">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-white text-gray-900 shadow-sm uppercase tracking-wider">
                                    {{ $related->category->name ?? 'Cat' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-lg font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('public.vehicles.show', $related->id) }}" class="hover:text-primary-600 transition">{{ $related->model }}</a>
                                </h3>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">{{ $related->brand }}</p>
                            
                            <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-auto">
                                <span class="text-lg font-black text-gray-900">R$ {{ number_format($related->category->daily_rate ?? 0, 2, ',', '.') }}<span class="text-xs font-semibold text-gray-400 uppercase tracking-widest block sm:inline"> /dia</span></span>
                                <a href="{{ route('public.vehicles.show', $related->id) }}" class="text-primary-600 font-bold text-sm hover:text-primary-800 transition">Ver</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        
    </div>
</div>
@endsection
