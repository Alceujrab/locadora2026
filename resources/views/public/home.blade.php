@extends('public.layouts.app')

@section('title', 'Aluguel de Veículos Sem Burocracia')

@section('content')
<!-- Hero Section -->
<div class="relative bg-white overflow-hidden">
    <div class="max-w-7xl mx-auto">
        <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
            <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                <div class="sm:text-center lg:text-left">
                    <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block xl:inline">Sua viagem começa</span>
                        <span class="block text-primary-600 xl:inline">com a Locadora 2026</span>
                    </h1>
                    <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                        Ampla frota de veículos revisados para você alugar sem burocracia. Encontre o carro perfeito para o seu dia a dia, sua viagem ou o seu negócio.
                    </p>
                    
                    <!-- Unified Search Bar -->
                    <div class="mt-8 bg-white p-4 sm:p-6 rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 max-w-3xl lg:mx-0 mx-auto">
                        <form action="{{ route('public.vehicles') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                            
                            <div class="flex-1">
                                <label for="home_category" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Categoria</label>
                                <select id="home_category" name="category_id" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-gray-50 font-medium">
                                    <option value="">Qualquer modelo</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex-1">
                                <label for="home_brand" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Marca</label>
                                <select id="home_brand" name="brand" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md bg-gray-50 font-medium">
                                    <option value="">Todas as marcas</option>
                                    @foreach($brands as $brandName)
                                        <option value="{{ $brandName }}">{{ $brandName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="sm:self-end">
                                <button type="submit" class="w-full sm:w-auto flex items-center justify-center px-8 py-3 h-[46px] border border-transparent text-base font-bold rounded-md text-white bg-primary-600 hover:bg-primary-700 transition shadow-sm">
                                    Buscar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
        <img class="h-56 w-full object-cover sm:h-72 md:h-96 lg:w-full lg:h-full" src="https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80" alt="Carros de luxo estacionados">
    </div>
</div>

<!-- Featured Vehicles -->
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-base font-semibold text-primary-600 tracking-wide uppercase">Destaques</h2>
            <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                Veículos Recomendados
            </p>
        </div>

        <div class="mt-10">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($featuredVehicles as $vehicle)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition duration-300 border border-gray-100 flex flex-col">
                        <div class="h-48 bg-gray-200 uppercase flex items-center justify-center text-gray-500 overflow-hidden relative group">
                            @if($vehicle->photos && count($vehicle->photos) > 0)
                                <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                            @else
                                <span class="text-xl font-medium tracking-widest text-gray-400">Sem Foto</span>
                            @endif
                            <div class="absolute top-0 right-0 m-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    {{ $vehicle->category->name ?? 'Categoria' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-6 flex-grow flex flex-col justify-between">
                            <div>
                                <h3 class="text-lg leading-6 font-medium text-gray-900 truncate">
                                    {{ $vehicle->model }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $vehicle->brand }} &bull; {{ $vehicle->year }}
                                </p>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div>
                                    <span class="text-2xl font-bold text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                                    <span class="text-sm font-medium text-gray-500">/dia</span>
                                </div>
                                <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 transition">
                                    Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum veículo encontrado</h3>
                        <p class="mt-1 text-sm text-gray-500">Tente buscar em outro momento ou contate nossa agência.</p>
                    </div>
                @endforelse
            </div>
            
            <div class="mt-10 text-center">
                <a href="{{ route('public.vehicles') }}" class="text-base font-medium text-primary-600 hover:text-primary-500 transition">
                    Ver Frota Completa <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
