@extends('public.layouts.app')

@section('title', $vehicle->model . ' - ' . $vehicle->brand)

@section('content')
<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('public.home') }}" class="hover:text-primary-600 transition">Início</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('public.vehicles') }}" class="ml-1 hover:text-primary-600 md:ml-2 transition">Frota</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 text-gray-900 font-semibold md:ml-2">{{ $vehicle->model }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Content: Vehicle Specs & Gallery -->
            <main class="w-full lg:w-2/3 space-y-8">
                
                <!-- Main Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl">{{ $vehicle->model }}</h1>
                            <p class="text-lg text-gray-500 mt-2 font-medium">{{ $vehicle->brand }} &bull; {{ $vehicle->year }}</p>
                        </div>
                        <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-bold bg-primary-50 text-primary-700 ring-1 ring-primary-600/20">
                            {{ $vehicle->category->name ?? 'Cat. Padrão' }}
                        </span>
                    </div>

                    <!-- Gallery -->
                    <div class="bg-gray-100 rounded-lg aspect-w-16 aspect-h-9 relative flex items-center justify-center overflow-hidden mb-8">
                        @if($vehicle->photos && count($vehicle->photos) > 0)
                            <img src="{{ asset('storage/' . $vehicle->photos[0]) }}" alt="{{ $vehicle->model }}" class="object-cover w-full h-full hover:scale-105 transition-transform duration-700">
                        @else
                            <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>

                    <!-- Key Specs Grid -->
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Especificações Principais</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-primary-200 transition">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Câmbio</span>
                            <span class="text-sm text-gray-900 font-bold mt-1">{{ $vehicle->transmission ?? 'Manual' }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-primary-200 transition">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Portas</span>
                            <span class="text-sm text-gray-900 font-bold mt-1">{{ $vehicle->doors }} Portas</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-primary-200 transition">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Assentos</span>
                            <span class="text-sm text-gray-900 font-bold mt-1">{{ $vehicle->passengers ?? 5 }} Lugares</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 flex flex-col items-center justify-center text-center group hover:border-primary-200 transition">
                            <svg class="w-8 h-8 text-gray-400 mb-2 group-hover:text-primary-500 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Combustível</span>
                            <span class="text-sm text-gray-900 font-bold mt-1">{{ $vehicle->fuel_type }}</span>
                        </div>
                    </div>

                    @if($vehicle->accessories && count($vehicle->accessories) > 0)
                    <div class="mt-10">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Acessórios Inclusos</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($vehicle->accessories as $accessory)
                                <li class="flex items-center text-gray-700 bg-green-50/50 px-3 py-2 rounded border border-green-100">
                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span class="font-medium text-sm">{{ $accessory }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Terms & Conditions Warning -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 flex gap-4">
                    <svg class="w-8 h-8 text-blue-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <div>
                        <h4 class="text-base font-bold text-blue-900">Requisitos para Locação</h4>
                        <p class="mt-2 text-sm text-blue-800 leading-relaxed">
                            O locatário deve possuir CNH válida há pelo menos 2 anos. Será exigido caução em cartão de crédito da própria titularidade no momento da retirada.
                        </p>
                    </div>
                </div>

            </main>

            <!-- Right Content: Sticky Booking Sidebar -->
            <aside class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 p-6 sticky top-6">
                    <div class="border-b border-gray-100 pb-6 mb-6">
                        <p class="text-sm text-gray-500 font-semibold uppercase tracking-wider mb-1">Valor da Diária</p>
                        <div class="flex items-end gap-2">
                            <span class="text-4xl font-extrabold text-gray-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}</span>
                            <span class="text-gray-500 font-medium pb-1">/dia</span>
                        </div>
                    </div>

                    <form action="#" method="GET" class="space-y-5" x-data="{
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
                        <!-- Date Pickers (Frontend Only Simulation) -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Retirada</label>
                            <input type="date" x-model="start" class="block w-full border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-gray-50" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Devolução</label>
                            <input type="date" x-model="end" class="block w-full border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 sm:text-sm bg-gray-50" required>
                        </div>
                        
                        <!-- Price Calculation Preview -->
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200" x-show="days() > 0" style="display: none;">
                            <div class="flex justify-between text-sm mb-2 text-gray-600">
                                <span>R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }} x <span x-text="days()"></span> dias</span>
                                <span class="font-bold">R$ <span x-text="total().toFixed(2).replace('.', ',')"></span></span>
                            </div>
                            <div class="flex justify-between text-sm text-green-600 font-medium mb-3">
                                <span>Seguro Básico</span>
                                <span>Incluso</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3 flex justify-between font-bold text-base text-gray-900">
                                <span>Total Previsto</span>
                                <span>R$ <span x-text="total().toFixed(2).replace('.', ',')"></span></span>
                            </div>
                        </div>

                        <a href="{{ route('moonshine.login') }}" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-lg shadow-sm text-base font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 mt-2">
                            Reservar Agora
                        </a>
                        
                        <p class="text-xs text-center text-gray-500 mt-4 leading-relaxed">
                            Você não será cobrado agora.<br>A confirmação é feita no portal do cliente via WhatsApp.
                        </p>
                    </form>
                </div>
            </aside>
            
        </div>

        @if(count($relatedVehicles) > 0)
        <!-- Related Vehicles -->
        <div class="mt-20 pt-10 border-t border-gray-200">
            <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Você também pode gostar</h2>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedVehicles as $related)
                    <a href="{{ route('public.vehicles.show', $related->id) }}" class="group block bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-lg transition duration-300">
                        <div class="w-full h-40 bg-gray-100 border-b border-gray-100 rounded-t-xl overflow-hidden relative">
                            @if($related->photos && count($related->photos) > 0)
                                <img src="{{ asset('storage/' . $related->photos[0]) }}" alt="{{ $related->model }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Sem Foto</span>
                                </div>
                            @endif
                            <div class="absolute top-2 right-2 px-2 py-1 rounded text-[10px] font-bold bg-white text-gray-900 shadow-sm">
                                R$ {{ number_format($related->category->daily_rate ?? 0, 0, ',', '.') }}/dia
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm text-gray-900 font-bold truncate group-hover:text-primary-600 transition">{{ $related->model }}</h3>
                            <p class="mt-1 text-xs text-gray-500">{{ $related->brand }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
