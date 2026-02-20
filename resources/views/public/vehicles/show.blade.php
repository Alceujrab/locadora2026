@extends('public.layouts.app')

@section('title', $vehicle->model . ' - ' . $vehicle->brand)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('public.home') }}" class="hover:text-primary-600">Início</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('public.vehicles') }}" class="ml-1 hover:text-primary-600 md:ml-2">Frota</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-gray-700 font-medium md:ml-2">{{ $vehicle->model }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Gallery -->
                <div class="bg-gray-200 aspect-w-4 aspect-h-3 md:aspect-none md:h-full relative flex items-center justify-center">
                    @if($vehicle->photos && count($vehicle->photos) > 0)
                        <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="object-cover w-full h-full">
                    @else
                        <span class="text-2xl tracking-widest text-gray-400 font-medium uppercase">Sem Foto</span>
                    @endif
                </div>

                <!-- Info Panel -->
                <div class="p-8 lg:p-12 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">{{ $vehicle->model }}</h1>
                                <p class="text-lg text-gray-500 mt-2">{{ $vehicle->brand }} &bull; {{ $vehicle->year }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                {{ $vehicle->category->name ?? '' }}
                            </span>
                        </div>

                        <div class="mt-8 border-t border-b border-gray-100 py-6 grid grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500">Placa</span>
                                <span class="text-base text-gray-900 font-semibold">{{ $vehicle->plate }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500">Cor</span>
                                <span class="text-base text-gray-900 font-semibold">{{ $vehicle->color }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500">Combustível</span>
                                <span class="text-base text-gray-900 font-semibold uppercase">{{ $vehicle->fuel_type }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-500">Portas / Assentos</span>
                                <span class="text-base text-gray-900 font-semibold">{{ $vehicle->doors }} Portas</span>
                            </div>
                        </div>

                        @if($vehicle->accessories && count($vehicle->accessories) > 0)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-900">Acessórios Inclusos</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach($vehicle->accessories as $accessory)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded border border-gray-200 text-xs font-medium text-gray-600 bg-gray-50">
                                        {{ $accessory }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-10 bg-gray-50 rounded-lg p-6 flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Diária a partir de</p>
                            <p class="text-3xl font-extrabold text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</p>
                        </div>
                        <a href="{{ route('moonshine.login') }}" class="px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition">
                            Reservar Agora
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(count($relatedVehicles) > 0)
        <!-- Related Vehicles -->
        <div class="mt-16">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Veículos Similares</h2>
            <div class="mt-6 grid grid-cols-1 gap-y-10 gap-x-6 sm:grid-cols-2 lg:grid-cols-3 xl:gap-x-8">
                @foreach($relatedVehicles as $related)
                    <div class="group relative bg-white border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition">
                        <div class="w-full min-h-48 bg-gray-200 aspect-w-1 aspect-h-1 rounded-t-lg overflow-hidden lg:h-48 lg:aspect-none flex items-center justify-center">
                            @if($related->photos && count($related->photos) > 0)
                                <img src="{{ asset('storage/' . $related->photos[0]) }}" alt="{{ $related->model }}" class="w-full h-full object-center object-cover lg:w-full lg:h-full group-hover:opacity-75 transition">
                            @else
                                <span class="text-sm text-gray-400 font-medium uppercase">Sem Foto</span>
                            @endif
                        </div>
                        <div class="p-4 flex justify-between">
                            <div>
                                <h3 class="text-sm text-gray-700 font-bold">
                                    <a href="{{ route('public.vehicles.show', $related->id) }}">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        {{ $related->model }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $related->brand }}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">R$ {{ number_format($related->category->daily_rate ?? 0, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
