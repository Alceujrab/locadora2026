@extends('public.layouts.app')

@section('title', 'Identificação - Passo 2')

@section('content')
<div class="bg-gray-50 py-12 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Bar -->
        <div class="mb-8">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Identificação</h1>
            <nav aria-label="Progress" class="mt-4">
                <ol role="list" class="flex items-center">
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-primary-600"></div>
                        </div>
                        <a href="{{ route('checkout.extras', ['vehicle_id' => session('checkout_reservation.vehicle_id'), 'start' => session('checkout_reservation.start_date'), 'end' => session('checkout_reservation.end_date')]) }}" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">1. Opcionais</span>
                    </li>
                    <li class="relative pr-8 sm:pr-20">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-gray-200"></div>
                        </div>
                        <a href="#" class="relative flex h-8 w-8 items-center justify-center rounded-full bg-primary-600 hover:bg-primary-700" aria-current="step">
                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                        </a>
                        <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-bold text-primary-600 w-max">2. Identificação</span>
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

        @if(session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-8 mt-12">
            
            <!-- Left Content: Identificação -->
            <main class="w-full lg:w-2/3" x-data="{ view: 'login' }">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex gap-4">
                        <button type="button" @click="view = 'login'" :class="{'text-primary-600 border-b-2 border-primary-600 font-bold': view === 'login', 'text-gray-500 font-medium hover:text-gray-700': view !== 'login'}" class="pb-2 px-2 transition">
                            Já sou Cliente
                        </button>
                        <button type="button" @click="view = 'register'" :class="{'text-primary-600 border-b-2 border-primary-600 font-bold': view === 'register', 'text-gray-500 font-medium hover:text-gray-700': view !== 'register'}" class="pb-2 px-2 transition">
                            Primeira Locação
                        </button>
                    </div>
                    
                    <!-- Login Form -->
                    <div x-show="view === 'login'" class="p-6 md:p-8">
                        <form action="{{ route('checkout.login') }}" method="POST" class="space-y-6 max-w-md mx-auto">
                            @csrf
                            <div>
                                <label for="email" class="block text-sm font-bold text-gray-700">E-mail</label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-bold text-gray-700">Senha</label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                                        Lembrar-me
                                    </label>
                                </div>
                                <div class="text-sm">
                                    <a href="#" class="font-medium text-primary-600 hover:text-primary-500">Esqueceu a senha?</a>
                                </div>
                            </div>

                            <div>
                                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                    Entrar e Continuar
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Register Form -->
                    <div x-show="view === 'register'" class="p-6 md:p-8" style="display: none;">
                        <form action="{{ route('checkout.register') }}" method="POST" class="space-y-6">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-2">
                                    <label for="reg_name" class="block text-sm font-bold text-gray-700">Nome Completo</label>
                                    <input type="text" name="name" id="reg_name" required value="{{ old('name') }}" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="reg_cpf_cnpj" class="block text-sm font-bold text-gray-700">CPF ou CNPJ</label>
                                    <input type="text" name="cpf_cnpj" id="reg_cpf_cnpj" required value="{{ old('cpf_cnpj') }}" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Apenas números">
                                </div>
                                
                                <div>
                                    <label for="reg_phone" class="block text-sm font-bold text-gray-700">Celular / WhatsApp</label>
                                    <input type="text" name="phone" id="reg_phone" required value="{{ old('phone') }}" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="(00) 00000-0000">
                                </div>

                                <div class="col-span-1 md:col-span-2">
                                    <label for="reg_email" class="block text-sm font-bold text-gray-700">E-mail</label>
                                    <input type="email" name="email" id="reg_email" required value="{{ old('email') }}" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="reg_password" class="block text-sm font-bold text-gray-700">Senha de Acesso</label>
                                    <input type="password" name="password" id="reg_password" required class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>

                                <div>
                                    <label for="reg_password_confirmation" class="block text-sm font-bold text-gray-700">Confirmar Senha</label>
                                    <input type="password" name="password_confirmation" id="reg_password_confirmation" required class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div class="mt-8 border-t border-gray-100 pt-6">
                                <button type="submit" class="w-full sm:w-auto flex justify-center py-3 px-8 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
                                    Cadastrar e Continuar
                                </button>
                                <p class="mt-3 text-xs text-gray-500">Ao se cadastrar você concorda com nossos Termos de Uso e Política de Privacidade da Locadora 2026.</p>
                            </div>
                        </form>
                    </div>

                </div>
            </main>

            <!-- Right Content: Order Summary Minimal -->
            <aside class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-[0_8px_30px_rgb(0,0,0,0.08)] border border-gray-100 sticky top-6 overflow-hidden">
                    <div class="bg-gray-50 p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Resumo da Reserva</h3>
                        <a href="{{ route('checkout.extras', ['vehicle_id' => session('checkout_reservation.vehicle_id'), 'start' => session('checkout_reservation.start_date'), 'end' => session('checkout_reservation.end_date')]) }}" class="text-xs text-primary-600 font-semibold hover:text-primary-800">Editar</a>
                    </div>
                    
                    <div class="p-6">
                        
                        <!-- Dates -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-500 font-medium">Retirada:</span>
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse(session('checkout_reservation.start_date'))->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500 font-medium">Devolução:</span>
                                <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse(session('checkout_reservation.end_date'))->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <!-- Pricing Breakdown -->
                        <div class="space-y-3 text-sm border-b border-gray-100 pb-4 mb-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total em Diárias</span>
                                <span class="font-medium text-gray-900">R$ {{ number_format($vehicleTotal, 2, ',', '.') }}</span>
                            </div>
                            <!-- Extras from session -->
                            @if(isset(session('checkout_reservation')['selected_extras']) && count(session('checkout_reservation')['selected_extras']) > 0)
                                @foreach(session('checkout_reservation')['selected_extras'] as $extra)
                                    <div class="flex justify-between text-primary-700">
                                        <span>+ {{ $extra['name'] }}</span>
                                        <span class="font-medium">R$ {{ number_format($extra['total'], 2, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <div class="flex items-end justify-between pt-2">
                            <span class="text-base font-bold text-gray-900">Total Previsto</span>
                            <span class="text-2xl font-extrabold text-primary-600">R$ {{ number_format($grandTotal, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </aside>
            
        </div>
    </div>
</div>
@endsection
