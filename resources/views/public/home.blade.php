@extends('public.layouts.app')

@section('title', 'Aluguel de carros sem burocracia')

@section('content')
{{-- ===================== HERO ===================== --}}
<section class="relative overflow-hidden bg-slate-950 text-white">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=2000&q=80"
             alt="" class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950/85 to-primary-900/60"></div>
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 -left-24 w-96 h-96 bg-secondary-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-40 lg:pt-28 lg:pb-48">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur border border-white/20 text-xs font-bold tracking-wider uppercase text-primary-300">
                <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                {{ $featuredVehicles->count() }} veículos disponíveis agora
            </span>

            <h1 class="mt-6 font-display text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.05]">
                Alugue seu próximo carro
                <span class="block bg-gradient-to-r from-primary-300 via-primary-400 to-secondary-400 text-transparent bg-clip-text">em poucos minutos.</span>
            </h1>
            <p class="mt-6 text-lg text-slate-300 max-w-xl leading-relaxed">
                Frota nova e revisada, contrato 100% digital e retirada rápida. Dirija a liberdade que você merece — sem burocracia, sem taxas escondidas.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-4">
                <a href="{{ route('public.vehicles') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-bold shadow-2xl shadow-primary-600/40 transition">
                    Ver toda a frota
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="{{ route('public.about') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white font-bold transition">
                    Como funciona
                </a>
            </div>

            <div class="mt-10 flex flex-wrap items-center gap-6 text-sm text-slate-300">
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Sem cartão de crédito</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Contrato digital</div>
                <div class="flex items-center gap-2"><svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Retirada em até 30min</div>
            </div>
        </div>
    </div>

    {{-- Search box flutuante --}}
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mb-24 lg:-mb-28">
        <div class="bg-white rounded-2xl shadow-2xl shadow-primary-900/30 p-5 sm:p-6 lg:p-8 ring-1 ring-slate-900/5">
            <form action="{{ route('public.vehicles') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Categoria</label>
                    <select name="category_id" class="w-full py-3 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Marca</label>
                    <select name="brand" class="w-full py-3 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Qualquer marca</option>
                        @foreach($brands as $brandName)
                            <option value="{{ $brandName }}">{{ $brandName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Transmissão</label>
                    <select name="transmission" class="w-full py-3 px-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-medium focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Indiferente</option>
                        <option value="automatica">Automática</option>
                        <option value="manual">Manual</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <button type="submit" class="w-full h-[50px] inline-flex justify-center items-center gap-2 px-6 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-600/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Buscar carro
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- ===================== TRUST STATS ===================== --}}
<section class="bg-white pt-40 pb-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <p class="font-display text-4xl font-black text-primary-600">+10</p>
                <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Anos no mercado</p>
            </div>
            <div>
                <p class="font-display text-4xl font-black text-primary-600">100%</p>
                <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Digital</p>
            </div>
            <div>
                <p class="font-display text-4xl font-black text-primary-600">4.9★</p>
                <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Avaliação média</p>
            </div>
            <div>
                <p class="font-display text-4xl font-black text-primary-600">24/7</p>
                <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Suporte</p>
            </div>
        </div>
    </div>
</section>

{{-- ===================== BENEFÍCIOS ===================== --}}
<section class="bg-slate-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Por que a {{ $companyName }}</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Tudo o que você precisa, nada que atrapalhe.</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $benefits = [
                    ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Reserva em 3 minutos', 'text' => 'Do clique ao carro nas mãos: processo enxuto, assinatura digital e retirada agendada.'],
                    ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Frota revisada', 'text' => 'Todos os carros passam por checagem completa antes de cada locação.'],
                    ['icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title' => 'Tarifas transparentes', 'text' => 'O valor que aparece é o valor que você paga. Nada de surpresas no checkout.'],
                    ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'title' => 'Atendimento humano', 'text' => 'Equipe pronta para resolver tudo, via WhatsApp ou no balcão das filiais.'],
                ];
            @endphp
            @foreach($benefits as $b)
                <div class="group bg-white rounded-2xl p-7 border border-slate-100 hover:border-primary-200 hover:shadow-xl hover:shadow-primary-100/50 transition-all">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-100 to-primary-50 flex items-center justify-center text-primary-600 mb-5 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $b['icon'] }}"/></svg>
                    </div>
                    <h3 class="font-display text-lg font-bold text-slate-900 mb-2">{{ $b['title'] }}</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $b['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== FROTA DESTAQUE ===================== --}}
<section class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-10 gap-4">
            <div>
                <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Destaques</span>
                <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Escolha seu próximo carro</h2>
            </div>
            <a href="{{ route('public.vehicles') }}" class="hidden sm:inline-flex items-center gap-1 text-primary-600 hover:text-primary-800 font-bold text-sm">
                Ver todos <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
            @forelse($featuredVehicles as $vehicle)
                <a href="{{ route('public.vehicles.show', $vehicle->id) }}" class="group bg-white rounded-2xl border border-slate-100 hover:border-primary-200 hover:shadow-2xl hover:shadow-primary-100/50 transition-all duration-500 overflow-hidden flex flex-col">
                    <div class="relative aspect-[16/10] bg-slate-100 overflow-hidden">
                        @if($vehicle->cover_photo)
                            <img src="{{ asset('storage/'.$vehicle->cover_photo) }}" alt="{{ $vehicle->model }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17l4 4 4-4m-4-5v9"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.39 18.39A5 5 0 0018 9h-1.26A8 8 0 103 16.3"/></svg>
                            </div>
                        @endif
                        <div class="absolute top-4 left-4 flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-white/95 backdrop-blur text-slate-800 shadow-sm">{{ $vehicle->category->name ?? 'Categoria' }}</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-500 text-white shadow-lg">Disponível</span>
                        </div>
                    </div>

                    <div class="p-6 flex-grow flex flex-col">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="font-display text-lg font-bold text-slate-900 leading-tight group-hover:text-primary-600 transition">
                                {{ $vehicle->brand }} {{ $vehicle->model }}
                            </h3>
                            <span class="shrink-0 ml-3 px-2 py-0.5 rounded-md text-xs font-bold text-slate-500 bg-slate-100">{{ $vehicle->year }}</span>
                        </div>
                        <p class="text-sm text-slate-500 mb-5">{{ $vehicle->color ?? 'Equipado e revisado' }}</p>

                        <div class="flex items-center gap-4 text-xs text-slate-500 pb-5 border-b border-slate-100 mb-5">
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 014-4m8-4a4 4 0 11-8 0 4 4 0 018 0z"/></svg>5 lugares</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>{{ $vehicle->transmission ?? 'Automática' }}</span>
                            <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>{{ $vehicle->fuel ?? 'Flex' }}</span>
                        </div>

                        <div class="flex items-end justify-between mt-auto">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">A partir de</p>
                                <p class="font-display text-2xl font-black text-slate-900">R$ {{ number_format($vehicle->category->daily_rate ?? 0, 2, ',', '.') }}<span class="text-sm font-medium text-slate-400">/dia</span></p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 group-hover:bg-primary-600 text-white text-sm font-bold transition">
                                Detalhes
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    <p class="text-slate-500">Nenhum veículo em destaque no momento.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10 text-center sm:hidden">
            <a href="{{ route('public.vehicles') }}" class="inline-flex items-center gap-2 w-full justify-center px-6 py-3 rounded-xl border border-slate-200 text-slate-800 font-bold">Ver todos os veículos</a>
        </div>
    </div>
</section>

{{-- ===================== COMO FUNCIONA ===================== --}}
<section class="bg-gradient-to-br from-slate-950 via-slate-900 to-primary-900 text-white py-20 overflow-hidden relative">
    <div class="absolute top-0 right-0 w-96 h-96 bg-primary-500/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-secondary-500/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-primary-400 font-bold tracking-widest uppercase text-xs">Simples e rápido</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black">Alugue em 3 passos.</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $steps = [
                    ['n'=>'01','t'=>'Escolha o carro','d'=>'Selecione o veículo ideal para sua viagem entre nossa frota.'],
                    ['n'=>'02','t'=>'Selecione as datas','d'=>'Escolha período de retirada e devolução. Veja o valor final na hora.'],
                    ['n'=>'03','t'=>'Retire e dirija','d'=>'Apresente os documentos, assine digitalmente e pegue as chaves.'],
                ];
            @endphp
            @foreach($steps as $s)
                <div class="bg-white/5 backdrop-blur border border-white/10 rounded-2xl p-8 hover:bg-white/10 transition">
                    <span class="font-display text-5xl font-black bg-gradient-to-r from-primary-400 to-secondary-400 bg-clip-text text-transparent">{{ $s['n'] }}</span>
                    <h3 class="mt-4 font-display text-xl font-bold">{{ $s['t'] }}</h3>
                    <p class="mt-2 text-sm text-slate-300 leading-relaxed">{{ $s['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== DEPOIMENTOS ===================== --}}
@if($testimonials->count() > 0)
<section class="bg-slate-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Depoimentos</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Quem aluga, recomenda.</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($testimonials->take(3) as $t)
                <div class="bg-white rounded-2xl border border-slate-100 p-8 shadow-sm hover:shadow-xl transition flex flex-col">
                    <div class="flex items-center gap-1 text-amber-400 mb-4">
                        @for($i=0;$i<($t->rating ?? 5);$i++)
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="text-slate-600 leading-relaxed flex-grow">&ldquo;{{ $t->content }}&rdquo;</p>
                    <div class="mt-6 pt-6 border-t border-slate-100 flex items-center gap-3">
                        @if($t->avatar)
                            <img src="{{ asset('storage/'.$t->avatar) }}" alt="{{ $t->name }}" class="w-11 h-11 rounded-full object-cover">
                        @else
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-bold">{{ strtoupper(substr($t->name,0,1)) }}</div>
                        @endif
                        <div>
                            <p class="font-bold text-sm text-slate-900">{{ $t->name }}</p>
                            <p class="text-xs text-slate-500">{{ $t->company ?? 'Cliente verificado' }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== FAQ ===================== --}}
@if($faqs->count() > 0)
<section class="bg-white py-20">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Dúvidas Frequentes</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Tire suas dúvidas.</h2>
        </div>

        <div class="space-y-3" x-data="{ active: null }">
            @foreach($faqs as $index => $faq)
                <div class="border border-slate-200 rounded-xl overflow-hidden bg-white hover:border-primary-200 transition">
                    <button type="button" @click="active = active === {{ $index }} ? null : {{ $index }}" class="w-full flex items-center justify-between p-5 text-left">
                        <span class="font-semibold text-slate-900 pr-4">{{ $faq->question }}</span>
                        <span class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-slate-600 transition-transform" :class="{ 'rotate-180': active === {{ $index }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </span>
                    </button>
                    <div x-show="active === {{ $index }}" x-collapse x-cloak class="px-5 pb-5 text-slate-600 leading-relaxed">{{ $faq->answer }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===================== CTA FINAL ===================== --}}
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-600 via-primary-700 to-slate-900 p-10 sm:p-16 text-white">
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-secondary-500/20 rounded-full blur-3xl"></div>

            <div class="relative max-w-3xl">
                <h2 class="font-display text-3xl sm:text-5xl font-black leading-[1.05]">
                    Pronto para acelerar?
                </h2>
                <p class="mt-4 text-lg text-primary-100 max-w-xl">
                    Reserve agora e retire na nossa filial mais próxima. Sem fila, sem letras miúdas, sem espera.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <a href="{{ route('public.vehicles') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-white text-primary-700 hover:bg-slate-50 font-bold shadow-2xl transition">
                        Ver frota disponível
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="{{ route('cliente.login') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur border border-white/20 text-white font-bold transition">
                        Já sou cliente
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
