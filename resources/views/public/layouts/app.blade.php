<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Locadora 2026 - O seu próximo veículo está aqui.">
    <title>{{ config('app.name', 'Locadora 2026') }} - @yield('title', 'Página Inicial')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <header class="bg-white shadow relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('public.home') }}" class="flex items-center gap-2">
                        <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-md">
                            L
                        </div>
                        <span class="font-bold text-xl tracking-tight text-gray-900">Locadora 2026</span>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('public.home') }}" class="text-sm font-medium text-gray-900 hover:text-primary-600 transition">Início</a>
                    <a href="{{ route('public.vehicles') }}" class="text-sm font-medium text-gray-600 hover:text-primary-600 transition">Nossa Frota</a>
                    
                    @auth
                        <a href="{{ route('moonshine.index') }}" class="px-4 py-2 rounded-md bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-medium transition shadow-sm">
                            Acessar Painel
                        </a>
                    @else
                        <a href="{{ route('moonshine.login') }}" class="px-4 py-2 rounded-md bg-primary-600 text-white hover:bg-primary-700 text-sm font-medium transition shadow-md">
                            Login Portal
                        </a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-primary-500 rounded flex items-center justify-center text-white font-bold">L</div>
                        <span class="font-bold text-lg">Locadora 2026</span>
                    </div>
                    <p class="text-gray-400 text-sm">
                        O seu próximo veículo está aqui. Alugue com facilidade, segurança e sem burocracia.
                    </p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase mb-4 text-gray-300">Links Úteis</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('public.home') }}" class="hover:text-primary-400 transition">Início</a></li>
                        <li><a href="{{ route('public.vehicles') }}" class="hover:text-primary-400 transition">Frota</a></li>
                        <li><a href="{{ route('moonshine.login') }}" class="hover:text-primary-400 transition">Painel do Cliente</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold tracking-wider uppercase mb-4 text-gray-300">Contato</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li>contato@locadora2026.com.br</li>
                        <li>(00) 00000-0000</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 flex text-sm text-gray-400 justify-between items-center">
                <p>&copy; {{ date('Y') }} Locadora 2026. Todos os direitos reservados.</p>
                <p>Feito com ❤️ por CutCode</p>
            </div>
        </div>
    </footer>

    <!-- Scripts Extra -->
    @stack('scripts')
</body>
</html>
