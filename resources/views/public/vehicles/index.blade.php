@extends('public.layouts.app')

@section('title', 'Nossa Frota')

@section('content')
<!-- Page Header -->
<div class="relative bg-gray-900 py-16 sm:py-24">
    <div class="absolute inset-0 overflow-hidden">
        <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Frota" class="w-full h-full object-cover opacity-20">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 opacity-90"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">Lista de Veículos</h1>
        <div class="flex items-center justify-center space-x-2 text-sm font-medium text-gray-300">
            <a href="{{ route('public.home') }}" class="hover:text-white transition">Início</a>
            <span>&bull;</span>
            <span class="text-primary-400">Frota</span>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <!-- Sidebar Filters -->
            <aside class="w-full lg:w-1/4">
                <div class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 sticky top-24">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                            Filtros
                        </h2>
                        @if(request()->anyFilled(['category_id', 'brand', 'price_max', 'transmission']))
                            <a href="{{ route('public.vehicles') }}" class="text-sm text-primary-600 hover:text-primary-800 font-bold transition">Limpar</a>
                        @endif
                    </div>

                    <form method="GET" action="{{ route('public.vehicles') }}" class="space-y-8">
                        <!-- Categorias -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-3">Categoria</label>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="radio" name="category_id" value="" {{ empty(request('category_id')) ? 'checked' : '' }} class="w-5 h-5 text-primary-600 border-gray-300 focus:ring-primary-500 rounded">
                                    <span class="ml-3 text-sm text-gray-600 group-hover:text-primary-600 transition">Todas</span>
                                </label>
                                @foreach($categories as $category)
                                    <label class="flex items-center cursor-pointer group">
                                        <input type="radio" name="category_id" value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'checked' : '' }} class="w-5 h-5 text-primary-600 border-gray-300 focus:ring-primary-500 rounded">
                                        <span class="ml-3 text-sm text-gray-600 group-hover:text-primary-600 transition">{{ $category->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sep --><hr class=" border-gray-100">

                        <!-- Marcas -->
                        <div>
                            <label for="brand" class="block text-sm font-bold text-gray-900 mb-3">Marca do Veículo</label>
                            <div class="relative">
                                <select id="brand" name="brand" class="block w-full pl-4 pr-10 py-3 text-base border-gray-200 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-xl bg-gray-50 text-gray-700 font-medium appearance-none">
                                    <option value="">Qualquer Marca</option>
                                    @foreach($brands as $brandName)
                                        <option value="{{ $brandName }}" {{ request('brand') == $brandName ? 'selected' : '' }}>
                                            {{ $brandName }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Sep --><hr class=" border-gray-100">

                        <!-- Transmissão -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-3">Transmissão</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="transmission" value="Automatico" class="peer sr-only" {{ request('transmission') == 'Automatico' ? 'checked' : '' }}>
                                    <div class="text-center px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 transition hover:bg-gray-50">
                                        Automático
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="transmission" value="Manual" class="peer sr-only" {{ request('transmission') == 'Manual' ? 'checked' : '' }}>
                                    <div class="text-center px-3 py-2 border border-gray-200 rounded-lg text-sm font-medium text-gray-600 peer-checked:bg-primary-50 peer-checked:border-primary-500 peer-checked:text-primary-700 transition hover:bg-gray-50">
                                        Manual
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Sep --><hr class=" border-gray-100">

                        <!-- Faixa de Preço -->
                        <div>
                            <label for="price_max" class="block text-sm font-bold text-gray-900 mb-4 flex justify-between">
                                Max. Diária
                                <span class="text-primary-600" id="price_val">R$ {{ request('price_max', $maxPrice ?? 1000) }}</span>
                            </label>
                            <input type="range" id="price_max" name="price_max" min="0" max="{{ $maxPrice ?? 1000 }}" value="{{ request('price_max', $maxPrice ?? 1000) }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-500" oninput="document.getElementById('price_val').textContent = 'R$ ' + this.value">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-bold rounded-xl text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all shadow-md shadow-primary-500/20">
                                Aplicar Filtros
                            </button>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Vehicles Main Grid -->
            <main class="w-full lg:w-3/4">
                
                <!-- Results Bar -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-center bg-white px-6 py-4 rounded-2xl shadow-sm border border-gray-100">
                    <p class="text-sm font-medium text-gray-500 mb-4 sm:mb-0">
                        Exibindo <span class="font-bold text-gray-900">{{ $vehicles->firstItem() ?? 0 }}</span> - <span class="font-bold text-gray-900">{{ $vehicles->lastItem() ?? 0 }}</span> de <span class="font-bold text-gray-900">{{ $vehicles->total() }}</span> veículos
                    </p>
                    
                    <div class="flex items-center space-x-3 w-full sm:w-auto">
                        <span class="text-sm font-bold text-gray-700 hidden sm:inline">Ordenar:</span>
                        <form method="GET" action="{{ route('public.vehicles') }}" class="w-full sm:w-auto">
                            <!-- Preserve filters -->
                            @foreach(request()->except(['sort', 'page']) as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            
                            <div class="relative">
                                <select name="sort" class="block w-full pl-4 pr-10 py-2 text-sm border border-gray-200 focus:outline-none rounded-xl bg-gray-50 text-gray-700 font-medium appearance-none" onchange="this.form.submit()">
                                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Adicionados Recentes</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8">
                    @forelse($vehicles as $vehicle)
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group overflow-hidden flex flex-col">
                            <div class="relative h-56 bg-gray-100 overflow-hidden">
                                @if($vehicle->photos && count($vehicle->photos) > 0)
                                    <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400 font-medium">Sem Imagem</div>
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
                                    
                                    <!-- Specs -->
                                    <div class="flex items-center gap-4 text-sm text-gray-600 mb-6 pb-6 border-b border-gray-50">
                                        <div class="flex items-center" title="Passageiros">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $vehicle->passengers ?? 5 }}
                                        </div>
                                        <div class="flex items-center" title="Transmissão">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            {{ substr($vehicle->transmission ?? 'Aut', 0, 3) }}.
                                        </div>
                                        <div class="flex items-center" title="Tipo">
                                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                            {{ $vehicle->fuel_type }}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium mb-0.5">Tarifa diária</p>
                                        <p class="text-2xl font-black text-gray-900 leading-none">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</p>
                                    </div>
                                    <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="inline-flex items-center justify-center p-3 sm:px-5 sm:py-2.5 rounded-xl text-sm font-bold bg-gray-900 text-white hover:bg-primary-600 transition group-hover:shadow-md">
                                        <span class="hidden sm:inline">Reservar</span>
                                        <svg class="w-5 h-5 sm:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 bg-white rounded-2xl border border-gray-100 flex flex-col items-center justify-center text-center px-4 shadow-sm">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                <svg class="h-10 w-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Nenhum veículo encontrado</h3>
                            <p class="text-gray-500 max-w-sm mx-auto mb-6">Infelizmente, não encontramos nenhum veículo com as especificações da sua busca atual.</p>
                            <a href="{{ route('public.vehicles') }}" class="inline-flex items-center px-6 py-3 rounded-xl text-base font-bold bg-primary-50 text-primary-700 hover:bg-primary-100 transition-colors">
                                Limpar Todos os Filtros
                            </a>
                        </div>
                    @endforelse
                </div>

                <!-- Custom Pagination -->
                @if($vehicles->hasPages())
                <div class="mt-12">
                    {{ $vehicles->appends(request()->query())->links() }}
                </div>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
