@extends('public.layouts.app')

@section('title', 'Nossa Frota')

@section('content')
<div class="bg-primary-700 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Nossa Frota</h1>
        <p class="mt-4 max-w-2xl mx-auto text-xl text-primary-100">
            Descubra o carro perfeito para a sua próxima jornada.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full lg:w-1/4">
            <div class="bg-white p-6 rounded-lg shadow border border-gray-100 sticky top-6">
                <div class="flex items-center justify-between border-b pb-3 mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Busca Avançada</h2>
                    @if(request()->anyFilled(['category_id', 'brand', 'price_max', 'transmission']))
                        <a href="{{ route('public.vehicles') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">Limpar</a>
                    @endif
                </div>

                <form method="GET" action="{{ route('public.vehicles') }}" class="space-y-5">
                    <!-- Categories -->
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Categoria</label>
                        <select id="category_id" name="category_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md shadow-sm">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Brand / Make -->
                    <div>
                        <label for="brand" class="block text-sm font-semibold text-gray-700 mb-2">Marca</label>
                        <select id="brand" name="brand" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md shadow-sm">
                            <option value="">Todas as Marcas</option>
                            @foreach($brands as $brandName)
                                <option value="{{ $brandName }}" {{ request('brand') == $brandName ? 'selected' : '' }}>
                                    {{ $brandName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Transmission -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Transmissão</label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input id="trans_auto" name="transmission" type="radio" value="Automatico" {{ request('transmission') == 'Automatico' ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                <label for="trans_auto" class="ml-3 text-sm text-gray-600">Automático</label>
                            </div>
                            <div class="flex items-center">
                                <input id="trans_man" name="transmission" type="radio" value="Manual" {{ request('transmission') == 'Manual' ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300">
                                <label for="trans_man" class="ml-3 text-sm text-gray-600">Manual</label>
                            </div>
                        </div>
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="price_max" class="block text-sm font-semibold text-gray-700 mb-2">Preço Máximo (Diária)</label>
                        <input type="range" id="price_max" name="price_max" min="0" max="{{ $maxPrice ?? 1000 }}" value="{{ request('price_max', $maxPrice ?? 1000) }}" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-primary-600" oninput="document.getElementById('price_val').textContent = 'R$ ' + this.value">
                        <div class="mt-2 text-sm text-gray-600 font-medium text-right" id="price_val">R$ {{ request('price_max', $maxPrice ?? 1000) }}</div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <button type="submit" class="w-full bg-primary-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition shadow">
                            Buscar Veículos
                        </button>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Vehicles Grid -->
        <main class="w-full lg:w-3/4">
            
            <div class="mb-4 flex justify-between items-center bg-gray-50 px-4 py-3 rounded-lg border border-gray-100">
                <span class="text-sm font-medium text-gray-700">Mostrando {{ $vehicles->firstItem() ?? 0 }} a {{ $vehicles->lastItem() ?? 0 }} de {{ $vehicles->total() }} veículos</span>
                
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Ordenar por:</span>
                    <form method="GET" action="{{ route('public.vehicles') }}" class="inline">
                        <!-- Keep existing specific filter params so sort doesn't kill filters -->
                        @foreach(request()->except(['sort', 'page']) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        
                        <select name="sort" class="block w-full pl-3 pr-8 py-1.5 text-sm border-gray-300 focus:outline-none rounded-md" onchange="this.form.submit()">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Mais Recentes</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($vehicles as $vehicle)
                    <div class="bg-white overflow-hidden shadow-sm rounded-xl hover:shadow-lg transition duration-300 border border-gray-100 flex flex-col group">
                        <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                            @if($vehicle->photos && count($vehicle->photos) > 0)
                                <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="object-cover w-full h-full group-hover:scale-110 transition duration-700">
                            @else
                                <span class="text-lg font-medium text-gray-400">Sem Foto</span>
                            @endif
                            <div class="absolute top-3 left-3 flex flex-col gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-white/90 text-primary-800 shadow-sm backdrop-blur-sm">
                                    {{ $vehicle->category->name ?? 'Padrão' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg leading-tight font-bold text-gray-900 group-hover:text-primary-600 transition">
                                    {{ $vehicle->model }}
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    {{ $vehicle->brand }} &bull; {{ $vehicle->year }}
                                </p>
                                
                                <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-xs text-gray-600 font-medium">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        {{ $vehicle->doors }} Portas
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                        {{ $vehicle->fuel_type }}
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                        {{ $vehicle->transmission ?? 'Manual' }}
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        {{ $vehicle->passengers ?? 5 }} Lugares
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-5 pt-4 flex items-center justify-between border-t border-gray-100">
                                <div>
                                    <span class="text-xl font-extrabold text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wide">/dia</span>
                                </div>
                                <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-primary-50 text-primary-600 hover:bg-primary-600 hover:text-white transition group-hover:shadow">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-20 bg-white rounded-xl border border-gray-200 border-dashed">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">Nenhum veículo bate com a busca</h3>
                        <p class="mt-1 text-sm text-gray-500">Tente ajustar seus filtros para ver mais resultados.</p>
                        <a href="{{ route('public.vehicles') }}" class="mt-6 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            Limpar Filtros
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($vehicles->hasPages())
            <div class="mt-10 bg-white px-4 py-3 border-t border-gray-200 sm:px-6 rounded-lg shadow-sm">
                {{ $vehicles->appends(request()->query())->links() }}
            </div>
            @endif
        </main>
    </div>
</div>
@endsection
