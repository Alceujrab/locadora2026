<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Elite Locadora — aluguel de veículos com frota nova, tarifas transparentes e atendimento sem burocracia.')">

    @include('public.partials._nav')

    <title>{{ $companyName }} — @yield('title', 'Aluguel de Veículos')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700|outfit:400,500,600,700,800,900" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>[x-cloak]{display:none !important;}</style>

    @stack('head')
</head>
<body class="bg-white text-slate-900 font-sans antialiased flex flex-col min-h-screen" x-data="{ mobileOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 10)">

    {{-- Top Bar --}}
    <div class="hidden md:block bg-slate-900 text-slate-300 text-xs">
        <div class="max-w-7xl mx-auto px-6 py-2 flex items-center justify-between">
            <div class="flex items-center gap-5">
                @if($companyPhone)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $companyPhone }}
                    </span>
                @endif
                @if($companyEmail)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $companyEmail }}
                    </span>
                @endif
            </div>
            <div class="flex items-center gap-4">
                @if($companyAddress)
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $companyAddress }}
                    </span>
                @endif
                @if($whatsappLink)
                    <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-emerald-400 hover:text-emerald-300 font-semibold">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zM6.597 20.193c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.888-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        WhatsApp
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-50 transition-all duration-300"
        :class="scrolled ? 'bg-white/95 backdrop-blur-xl shadow-md border-b border-slate-100' : 'bg-white border-b border-slate-100'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ route('public.home') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-black text-lg shadow-lg shadow-primary-600/30 group-hover:scale-105 transition">
                        {{ strtoupper(substr($companyName, 0, 1)) }}
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-display font-black text-lg text-slate-900 tracking-tight">{{ $companyName }}</span>
                        <span class="text-[11px] font-semibold text-slate-400 tracking-widest uppercase">Aluguel de Veículos</span>
                    </div>
                </a>

                @php
                    $navItems = [
                        ['route' => 'public.home', 'label' => 'Início'],
                        ['route' => 'public.vehicles', 'label' => 'Frota'],
                        ['route' => 'public.about', 'label' => 'Sobre'],
                        ['route' => 'public.contact', 'label' => 'Contato'],
                    ];
                @endphp

                <nav class="hidden lg:flex items-center gap-1">
                    @foreach($navItems as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                            class="relative px-4 py-2 text-sm font-semibold rounded-lg transition {{ $active ? 'text-primary-700' : 'text-slate-700 hover:text-primary-600' }}">
                            {{ $item['label'] }}
                            @if($active)
                                <span class="absolute bottom-0 left-4 right-4 h-0.5 bg-primary-600 rounded-full"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="flex items-center gap-3">
                    @auth('web')
                        <a href="{{ route('cliente.dashboard') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Minha Conta
                        </a>
                    @else
                        <a href="{{ route('cliente.login') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 text-slate-700 hover:text-primary-600 text-sm font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Entrar
                        </a>
                    @endauth
                    <a href="{{ route('public.vehicles') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold shadow-lg shadow-primary-600/25 transition">
                        Reservar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <button type="button" class="lg:hidden p-2 -mr-2 text-slate-700" @click="mobileOpen = !mobileOpen" aria-label="Menu">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile nav --}}
        <div x-show="mobileOpen" x-cloak x-transition class="lg:hidden border-t border-slate-100 bg-white" @click.outside="mobileOpen = false">
            <nav class="max-w-7xl mx-auto px-4 py-3 space-y-1">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->routeIs($item['route']) ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-50' }}">{{ $item['label'] }}</a>
                @endforeach
                @auth('web')
                    <a href="{{ route('cliente.dashboard') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Minha Conta</a>
                @else
                    <a href="{{ route('cliente.login') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Entrar</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-grow">
        @if(session('success'))
            <div class="bg-emerald-50 border-b border-emerald-200">
                <div class="max-w-7xl mx-auto px-4 py-3 text-sm text-emerald-800 font-medium flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @yield('content')
    </main>

    {{-- Floating WhatsApp --}}
    @if($whatsappLink)
        <a href="{{ $whatsappLink }}" target="_blank" rel="noopener"
            class="fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center shadow-2xl shadow-emerald-500/40 hover:scale-110 transition"
            aria-label="Fale no WhatsApp">
            <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zM6.597 20.193c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.888-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        </a>
    @endif

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-300 mt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
                <div>
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-black shadow-lg">
                            {{ strtoupper(substr($companyName, 0, 1)) }}
                        </div>
                        <span class="font-display font-black text-lg text-white">{{ $companyName }}</span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Frota nova, tarifas transparentes e contrato 100% digital. Seu próximo carro a um clique de distância.
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-bold tracking-widest uppercase text-white mb-5">Navegação</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('public.home') }}" class="hover:text-primary-400 transition">Início</a></li>
                        <li><a href="{{ route('public.vehicles') }}" class="hover:text-primary-400 transition">Nossa Frota</a></li>
                        <li><a href="{{ route('public.about') }}" class="hover:text-primary-400 transition">Sobre nós</a></li>
                        <li><a href="{{ route('public.contact') }}" class="hover:text-primary-400 transition">Contato</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-bold tracking-widest uppercase text-white mb-5">Conta</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('cliente.login') }}" class="hover:text-primary-400 transition">Portal do Cliente</a></li>
                        <li><a href="{{ route('cliente.login') }}" class="hover:text-primary-400 transition">Minhas Reservas</a></li>
                        <li><a href="{{ route('cliente.login') }}" class="hover:text-primary-400 transition">Faturas & Pagamentos</a></li>
                        <li><a href="{{ route('cliente.login') }}" class="hover:text-primary-400 transition">Multas</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-bold tracking-widest uppercase text-white mb-5">Contato</h3>
                    <ul class="space-y-3 text-sm">
                        @if($companyAddress)
                            <li class="flex gap-2 items-start"><svg class="w-4 h-4 mt-0.5 text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ $companyAddress }}</li>
                        @endif
                        @if($companyPhone)
                            <li class="flex gap-2 items-center"><svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $companyPhone }}</li>
                        @endif
                        @if($companyEmail)
                            <li class="flex gap-2 items-center"><svg class="w-4 h-4 text-primary-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $companyEmail }}</li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ $companyName }}. Todos os direitos reservados.</p>
                <p>Plataforma desenvolvida sob medida.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
