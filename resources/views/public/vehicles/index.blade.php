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
    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-lg shadow mb-8 flex items-center justify-between">
        <div>
            <span class="text-sm text-gray-500 font-medium">Filtrar por Categoria:</span>
        </div>
        <form method="GET" action="{{ route('public.vehicles') }}" class="flex items-center gap-4">
            <select name="category_id" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md" onchange="this.form.submit()">
                <option value="">Todas as Categorias</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }} (R$ {{ number_format($category->daily_rate, 2, ',', '.') }}/dia)
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    <!-- Vehicles Grid -->
    <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        @forelse($vehicles as $vehicle)
            <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition duration-300 border border-gray-100 flex flex-col">
                <div class="h-48 bg-gray-200 uppercase flex items-center justify-center text-gray-500 overflow-hidden relative group">
                    @if($vehicle->photos && count($vehicle->photos) > 0)
                        <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="object-cover w-full h-full group-hover:scale-105 transition duration-500">
                    @else
                        <span class="text-xl font-medium tracking-widest text-gray-400">Sem Foto</span>
                    @endif
                    <div class="absolute top-0 right-0 m-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary-100 text-primary-800">
                            {{ $vehicle->category->name ?? '' }}
                        </span>
                    </div>
                </div>
                <div class="p-5 flex-grow flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900 truncate">
                            {{ $vehicle->model }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $vehicle->brand }} &bull; {{ $vehicle->year }}
                        </p>
                        
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                {{ $vehicle->doors }} Portas
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                {{ $vehicle->fuel_type }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 border-t border-gray-100 pt-4 flex items-center justify-between">
                        <div>
                            <span class="text-xl font-bold text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                            <span class="text-xs font-medium text-gray-500">/dia</span>
                        </div>
                        <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded text-white bg-gray-900 hover:bg-gray-800 transition">
                            Alugar
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white rounded-lg border border-gray-200 border-dashed">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum veículo encontrado para este filtro</h3>
                <a href="{{ route('public.vehicles') }}" class="mt-3 block text-sm font-medium text-primary-600 hover:text-primary-500">Limpar filtros</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $vehicles->appends(request()->query())->links() }}
    </div>
</div>
@endsection
