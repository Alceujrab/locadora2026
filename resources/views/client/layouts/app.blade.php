<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Portal do Cliente') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css'])
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50 font-sans antialiased text-slate-900" x-data="{ mobileMenuOpen: false }">

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
        <div x-show="mobileMenuOpen" x-transition.opacity class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm"></div>

        <div class="fixed inset-0 flex">
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-in-out duration-300 transform" 
                 x-transition:enter-start="-translate-x-full" 
                 x-transition:enter-end="translate-x-0" 
                 x-transition:leave="transition ease-in-out duration-300 transform" 
                 x-transition:leave-start="translate-x-0" 
                 x-transition:leave-end="-translate-x-full" 
                 class="relative mr-16 flex w-full max-w-xs flex-1">
                
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 p-2.5">
                        <span class="sr-only">Fechar sidebar</span>
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 ring-1 ring-white/10">
                    <div class="flex h-16 shrink-0 items-center border-b border-slate-100">
                        <img class="h-8 w-auto" src="{{ asset('vendor/moonshine/logo-small.svg') }}" alt="Locadora">
                        <span class="ml-3 font-display font-semibold text-lg">Portal</span>
                    </div>
                    <nav class="flex flex-1 flex-col">
                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                            <li>
                                <ul role="list" class="-mx-2 space-y-1">
                                    <li>
                                        <a href="{{ route('cliente.dashboard') }}" class="{{ request()->routeIs('cliente.dashboard') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.dashboard') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                            </svg>
                                            Início
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cliente.invoices') }}" class="{{ request()->routeIs('cliente.invoices') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.invoices') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Faturas & Pagamentos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cliente.contracts') }}" class="{{ request()->routeIs('cliente.contracts') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.contracts') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            Contratos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cliente.reservations') }}" class="{{ request()->routeIs('cliente.reservations') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.reservations') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                            </svg>
                                            Reservas
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cliente.service-orders') }}" class="{{ request()->routeIs('cliente.service-orders') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.service-orders') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.386 3.075A.478.478 0 015 17.764V3h14v14.764a.478.478 0 01-1.034.481l-5.386-3.075a.478.478 0 00-.478 0z" />
                                            </svg>
                                            Ordens de Servico
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cliente.support') }}" class="{{ request()->routeIs('cliente.support') ? 'bg-gray-50 text-primary-600' : 'text-gray-700 hover:text-primary-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold transition">
                                            <svg class="{{ request()->routeIs('cliente.support') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }} h-6 w-6 shrink-0 transition" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.436 3 12c0 1.967.653 3.765 1.761 5.23l-1.071 2.946a.75.75 0 00.941.986l3.051-1.077A9.043 9.043 0 0012 20.25z" />
                                            </svg>
                                            Meus Chamados
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-slate-200 bg-white px-6">
            <div class="flex h-16 shrink-0 items-center justify-between border-b border-slate-100">
                <a href="{{ route('cliente.dashboard') }}" class="flex items-center gap-3">
                    <img class="h-8 w-auto text-primary-600" src="{{ asset('vendor/moonshine/logo-small.svg') }}" alt="Locadora">
                    <span class="font-display font-black text-xl text-gray-900 tracking-tight">Portal</span>
                </a>
            </div>
            <nav class="flex flex-1 flex-col pt-4">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2 space-y-2">
                    <li>
                                <a href="{{ route('cliente.dashboard') }}" class="{{ request()->routeIs('cliente.dashboard') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.dashboard') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                    Início
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cliente.invoices') }}" class="{{ request()->routeIs('cliente.invoices') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.invoices') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Faturas e Pagamentos
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cliente.contracts') }}" class="{{ request()->routeIs('cliente.contracts') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.contracts') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Contratos e Checklists
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cliente.reservations') }}" class="{{ request()->routeIs('cliente.reservations') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.reservations') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                                    </svg>
                                    Reservas
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('cliente.service-orders') }}" class="{{ request()->routeIs('cliente.service-orders') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.service-orders') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17l-5.386 3.075A.478.478 0 015 17.764V3h14v14.764a.478.478 0 01-1.034.481l-5.386-3.075a.478.478 0 00-.478 0z" />
                                    </svg>
                                    Ordens de Servico
                                </a>
                            </li>
                            <li class="pt-4 mt-4 border-t border-slate-100">
                                <a href="{{ route('cliente.support') }}" class="{{ request()->routeIs('cliente.support') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:text-primary-600 transition-colors hover:bg-gray-50 font-medium' }} group flex gap-x-3 rounded-xl p-3 text-sm leading-6">
                                    <svg class="{{ request()->routeIs('cliente.support') ? 'text-primary-600' : 'text-slate-400 group-hover:text-primary-600 transition-colors' }} h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.436 3 12c0 1.967.653 3.765 1.761 5.23l-1.071 2.946a.75.75 0 00.941.986l3.051-1.077A9.043 9.043 0 0012 20.25z" />
                                    </svg>
                                    Abrir/Ver Chamados
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <li class="mt-auto">
                        <div class="rounded-2xl p-5 bg-gray-900 border border-gray-800 shadow-xl overflow-hidden relative">
                            <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary-500/20 rounded-full blur-2xl"></div>
                            <h3 class="text-white font-display font-bold text-lg relative z-10">Locadora 2026</h3>
                            <p class="text-gray-400 text-xs mt-1 relative z-10 leading-relaxed font-medium">Suporte Seg-Sáb 08h-18h</p>
                            <a href="https://wa.me/5511999999999" target="_blank" class="mt-4 inline-flex items-center text-xs font-bold text-gray-900 bg-white hover:bg-primary-50 px-3 py-2 rounded-lg transition-colors relative z-10 shadow-sm w-full justify-center">
                                <svg class="w-4 h-4 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.888-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                Falar no WhatsApp
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            
            <form action="{{ route('cliente.logout') }}" method="POST" class="mt-4 pb-6">
                @csrf
                <button type="submit" class="w-full relative flex items-center gap-x-3 px-3 py-2 text-sm font-semibold leading-6 text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                    <svg class="h-5 w-5 shrink-0 text-slate-400 group-hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Sair da Conta
                </button>
            </form>
        </div>
    </div>

    <div class="lg:pl-72">
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-slate-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
            <button type="button" class="-m-2.5 p-2.5 text-slate-700 lg:hidden" @click="mobileMenuOpen = true">
                <span class="sr-only">Abrir menu</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Separator -->
            <div class="h-6 w-px bg-slate-200 lg:hidden" aria-hidden="true"></div>

            <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6 justify-between items-center">
                <h1 class="text-xl font-display font-semibold text-slate-900 truncate">
                    @yield('title', 'Painel')
                </h1>
                
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <div class="flex items-center gap-x-4 lg:gap-x-6 bg-white px-3 py-1.5 rounded-full border border-gray-200 shadow-sm">
                        <span class="sr-only">Seu Perfil</span>
                        <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold border border-primary-200">
                            {{ substr(Auth::guard('web')->user()->name, 0, 1) }}
                        </div>
                        <span class="hidden lg:flex lg:items-center">
                            <span class="text-sm font-bold leading-6 text-gray-900 font-display" aria-hidden="true">{{ Auth::guard('web')->user()->name }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <main class="py-10">
            <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                @if(session('success'))
                <div class="rounded-xl bg-green-50 p-4 mb-6 border border-green-100">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
