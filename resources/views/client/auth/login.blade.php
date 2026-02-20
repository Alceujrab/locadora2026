@extends('public.layouts.app')
@section('title', 'Acesso ao Portal do Cliente')

@section('content')
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-slate-50">
        
        <!-- Formulário Lado Esquerdo -->
        <div class="flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 bg-white shadow-[10px_0_30px_rgba(0,0,0,0.02)] z-10">
            <div class="w-full max-w-md space-y-8">
                
                <div class="text-center">
                    <img class="mx-auto h-16 w-auto mb-6" src="{{ asset('vendor/moonshine/logo-small.svg') }}" alt="Locadora 2026">
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-slate-900 font-display">
                        Portal do Cliente
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Acesse para gerenciar seus contratos, reservas e faturas.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="rounded-xl bg-red-50 p-4 border border-red-100">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Falha na Autenticação
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul role="list" class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <form class="mt-8 space-y-6" action="{{ route('cliente.login') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4 rounded-md shadow-sm">
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-mail Cadastrado</label>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                value="{{ old('email') }}"
                                class="relative block w-full rounded-xl border-0 py-3 px-4 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 transition-shadow" 
                                placeholder="seu@email.com">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Senha</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="relative block w-full rounded-xl border-0 py-3 px-4 text-slate-900 ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 transition-shadow" 
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-purple-600 focus:ring-purple-600">
                            <label for="remember" class="ml-2 block text-sm text-slate-900">Lembrar de mim</label>
                        </div>

                        <div class="text-sm">
                            <a href="#" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">Esqueceu a senha?</a>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors">
                            Acessar o Portal
                            <svg class="ml-2 -mr-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500">
                        Ainda não é cliente? <a href="{{ route('public.vehicles') }}" class="font-medium text-purple-600 hover:text-purple-500">Alugue um veículo agora</a>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Banner Lado Direito -->
        <div class="hidden lg:flex relative w-full h-full bg-slate-900 items-center justify-center p-12 overflow-hidden">
            <!-- Background Image -->
            <img class="absolute inset-0 w-full h-full object-cover opacity-30 mix-blend-overlay" src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=1920&q=80" alt="Dashboard">
            
            <!-- Graphic Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-purple-900/80 to-slate-900/60 mix-blend-multiply"></div>
            
            <!-- Content -->
            <div class="relative z-10 w-full max-w-lg text-white">
                <blockquote class="space-y-6">
                    <p class="text-2xl font-medium leading-relaxed font-display">
                        "Ter acesso total às minhas faturas, via PIX na hora, e poder baixar os documentos dos carros a qualquer momento mudou o jogo pra minha empresa."
                    </p>
                    <footer class="flex items-center space-x-4">
                        <div class="h-12 w-12 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <svg class="h-6 w-6 text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-white">Roberto Ferreira</div>
                            <div class="text-sm text-purple-200">Cliente Corporativo</div>
                        </div>
                    </footer>
                </blockquote>
                
                <div class="mt-12 grid grid-cols-2 gap-6 pt-12 border-t border-white/10">
                    <div>
                        <div class="flex items-center space-x-2 text-purple-300 mb-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="font-medium">100% Seguro</span>
                        </div>
                        <p class="text-sm text-slate-300">Seus dados e históricos protegidos.</p>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2 text-purple-300 mb-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            <span class="font-medium">Praticidade</span>
                        </div>
                        <p class="text-sm text-slate-300">Faturas, Chamados e Contratos em 1 clique.</p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
@endsection
