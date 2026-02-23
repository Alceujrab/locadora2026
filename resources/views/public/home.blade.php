@extends('public.layouts.app')

@section('title', 'Aluguel de Veículos Sem Burocracia')

@section('content')
<!-- Hero Section (Carento Style) -->
<div class="relative bg-gray-900 overflow-hidden py-24 sm:py-32 lg:py-40">
    <div class="absolute inset-0">
        <img class="w-full h-full object-cover opacity-30" src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Carros de luxo estacionados">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 opacity-80"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white z-10">
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight mb-4">
            A forma mais fácil de <span class="text-primary-500 text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-primary-600">Alugar um Carro</span>
        </h1>
        <p class="max-w-2xl mx-auto text-lg sm:text-xl text-gray-300 mb-10">
            Descubra a liberdade na estrada. Escolha entre nossa ampla frota e reserve em minutos com as melhores tarifas garantidas.
        </p>

        <!-- Search Box (Horizon) -->
        <div class="bg-white rounded-2xl shadow-2xl p-4 sm:p-6 max-w-4xl mx-auto text-left transform translate-y-12 sm:translate-y-16">
            <form action="{{ route('public.vehicles') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 xl:gap-6 items-end">
                
                <div>
                    <label for="home_category" class="block text-sm font-semibold text-gray-700 mb-2">Qual o tipo do carro?</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>
                        </div>
                        <select id="home_category" name="category_id" class="block w-full pl-10 pr-3 py-3 border-gray-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-gray-900 bg-gray-50/50">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="home_brand" class="block text-sm font-semibold text-gray-700 mb-2">Pretende alguma marca?</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <select id="home_brand" name="brand" class="block w-full pl-10 pr-3 py-3 border-gray-200 rounded-xl focus:ring-primary-500 focus:border-primary-500 text-gray-900 bg-gray-50/50">
                            <option value="">Qualquer Marca</option>
                            @foreach($brands as $brandName)
                                <option value="{{ $brandName }}">{{ $brandName }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div>
                    <button type="submit" class="w-full h-[50px] inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-all">
                        Encontrar Veículo
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Brands Carousel (Spacing to account for floating search box) -->
<div class="bg-white pt-24 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-sm font-semibold uppercase tracking-wider text-gray-400 mb-8">Marcas de confiança em nossa frota</p>
        <div class="flex flex-wrap justify-center gap-8 opacity-60 grayscale hover:grayscale-0 transition-all duration-300">
            @foreach($brands as $brand)
                <span class="text-2xl font-bold text-gray-400">{{ $brand }}</span>
            @endforeach
        </div>
    </div>
</div>

<!-- How it Works section -->
<section class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary-600 font-bold tracking-wider uppercase text-sm">Processo Simples</span>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900 sm:text-4xl">Como funciona o aluguel?</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-xl transition-shadow border border-gray-100 relative">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-6 shrink-0">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">1. Escolha o Veículo</h3>
                <p class="text-gray-500">Selecione o carro perfeito para sua viagem entre nossa frota variada e bem cuidada.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-xl transition-shadow border border-gray-100">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-6 shrink-0">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">2. Selecione as Datas</h3>
                <p class="text-gray-500">Defina o período de locação, escolha os opcionais e os horários de retirada e devolução.</p>
            </div>
            <div class="bg-white rounded-2xl p-8 text-center shadow-sm hover:shadow-xl transition-shadow border border-gray-100">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-6 shrink-0">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">3. Retire e Dirija!</h3>
                <p class="text-gray-500">Apresente seus documentos na agência, assine o contrato digitalmente e pegue as chaves.</p>
            </div>
        </div>
    </div>
</section>

<!-- Featured Vehicles -->
<section class="bg-white py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-10">
            <div>
                <span class="text-primary-600 font-bold tracking-wider uppercase text-sm">Destaques Diários</span>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900">Nossa Frota Recomendada</h2>
            </div>
            <a href="{{ route('public.vehicles') }}" class="hidden sm:inline-flex items-center font-medium text-primary-600 hover:text-primary-700">
                Ver todos os veículos <span aria-hidden="true" class="ml-1">&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($featuredVehicles as $vehicle)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden flex flex-col">
                    <div class="relative h-56 bg-gray-100 overflow-hidden">
                        @if($vehicle->photos && count($vehicle->photos) > 0)
                            <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">Sem Imagem</div>
                        @endif
                        
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-white text-gray-900 shadow-sm">
                                {{ $vehicle->category->name ?? 'Categoria' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="hover:text-primary-600 transition">{{ $vehicle->model }}</a>
                                </h3>
                                <div class="bg-gray-50 px-2 py-1 rounded text-xs font-semibold text-gray-600 border border-gray-100">
                                    {{ $vehicle->year }}
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mb-4">{{ $vehicle->brand }}</p>
                            
                            <!-- Specs Row -->
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-50">
                                <div class="flex items-center" title="Passageiros">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    5
                                </div>
                                <div class="flex items-center" title="Transmissão">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Aut.
                                </div>
                                <div class="flex items-center" title="Malas">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    2
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 mb-0.5">Tarifa diária</p>
                                <p class="text-2xl font-black text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</p>
                            </div>
                            <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="inline-flex items-center justify-center p-3 sm:px-5 sm:py-2.5 rounded-xl text-sm font-bold bg-gray-900 text-white hover:bg-primary-600 transition">
                                <span class="hidden sm:inline">Reservar</span>
                                <svg class="w-5 h-5 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-3 text-center py-12 bg-gray-50 rounded-2xl">
                    <p class="text-gray-500">Nenhum veículo em destaque no momento.</p>
                </div>
            @endforelse
        </div>
        
        <div class="mt-8 text-center sm:hidden">
            <a href="{{ route('public.vehicles') }}" class="inline-flex items-center justify-center w-full px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50">
                Ver todos os veículos
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
@if($testimonials->count() > 0)
<section class="bg-gray-50 py-16 sm:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <span class="text-primary-600 font-bold tracking-wider uppercase text-sm">Feedback</span>
            <h2 class="mt-2 text-3xl font-extrabold text-gray-900">O que nossos clientes dizem</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($testimonials->take(3) as $testimonial)
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center mb-4 space-x-1 text-yellow-400">
                        @for($i = 0; $i < $testimonial->rating; $i++)
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        @endfor
                    </div>
                    <p class="text-gray-600 italic mb-6">"{{ $testimonial->content }}"</p>
                </div>
                <div class="flex items-center">
                    @if($testimonial->avatar)
                        <img class="h-12 w-12 rounded-full object-cover mr-4" src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->name }}">
                    @else
                        <div class="h-12 w-12 rounded-full bg-primary-100 text-primary-600 flex flex-shrink-0 items-center justify-center font-bold text-lg mr-4">
                            {{ substr($testimonial->name, 0, 1) }}
                        </div>
                    @endif
                    <div>
                        <h4 class="text-sm font-bold text-gray-900">{{ $testimonial->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $testimonial->company ?? 'Cliente' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- FAQ Section -->
@if($faqs->count() > 0)
<section class="bg-white py-16 sm:py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-gray-900">Perguntas Frequentes</h2>
            <p class="mt-4 text-lg text-gray-500">Tire suas dúvidas antes de alugar.</p>
        </div>
        
        <div class="space-y-4" x-data="{ active: null }">
            @foreach($faqs as $index => $faq)
            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <button @click="active = active === {{ $index }} ? null : {{ $index }}" class="flex justify-between w-full p-5 sm:p-6 bg-white hover:bg-gray-50 text-left transition items-center">
                    <span class="text-base sm:text-lg font-semibold text-gray-900 pr-4">{{ $faq->question }}</span>
                    <span class="ml-6 flex-shrink-0 flex items-center shrink-0">
                        <svg class="h-6 w-6 text-gray-400 transform transition-transform" :class="{ 'rotate-180': active === {{ $index }} }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div x-show="active === {{ $index }}" style="display: none;" class="px-5 sm:px-6 pb-6 pt-0 bg-white">
                    <p class="text-gray-500 text-base">{{ $faq->answer }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
