@extends('public.layouts.app')
@section('title', 'Sobre nós')

@section('content')
<section class="relative bg-slate-950 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-30">
        <img src="https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=2000&q=80" class="w-full h-full object-cover" alt="">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-slate-950/40"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
        <span class="inline-block px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-widest text-primary-300">Sobre a {{ $companyName }}</span>
        <h1 class="mt-6 font-display text-4xl sm:text-6xl font-black tracking-tight">Mais que locação.<br><span class="bg-gradient-to-r from-primary-300 to-secondary-400 bg-clip-text text-transparent">Mobilidade com propósito.</span></h1>
        <p class="mt-6 max-w-2xl mx-auto text-slate-300 text-lg">Há mais de uma década facilitando deslocamentos de famílias, profissionais e empresas.</p>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">Nossa história</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Construída sobre confiança e bom atendimento.</h2>
            <p class="mt-6 text-slate-600 leading-relaxed">
                Nascemos com o propósito de simplificar o aluguel de veículos no interior do Brasil. Hoje, somos referência em atendimento ágil, transparência nas tarifas e cuidado com cada carro que entra na nossa frota.
            </p>
            <p class="mt-4 text-slate-600 leading-relaxed">
                Nossa tecnologia elimina o papel e a fila: reserva, assinatura e pagamento são 100% digitais. Do outro lado da tela, uma equipe que vive para resolver.
            </p>

            <div class="mt-10 grid grid-cols-2 gap-6">
                <div>
                    <p class="font-display text-4xl font-black text-primary-600">{{ $stats['vehicles'] ?? 0 }}+</p>
                    <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Veículos</p>
                </div>
                <div>
                    <p class="font-display text-4xl font-black text-primary-600">{{ $stats['customers'] ?? 0 }}+</p>
                    <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Clientes</p>
                </div>
                <div>
                    <p class="font-display text-4xl font-black text-primary-600">{{ $stats['rentals'] ?? 0 }}+</p>
                    <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Locações</p>
                </div>
                <div>
                    <p class="font-display text-4xl font-black text-primary-600">{{ $stats['cities'] ?? 1 }}</p>
                    <p class="mt-1 text-sm font-semibold text-slate-500 uppercase tracking-wider">Cidades</p>
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl">
                <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=900&q=80" class="w-full h-full object-cover" alt="">
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-5 ring-1 ring-slate-100 hidden sm:block">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Avaliação 4.9/5</p>
                        <p class="text-xs text-slate-500">Por clientes reais</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-primary-600 font-bold tracking-widest uppercase text-xs">O que nos move</span>
            <h2 class="mt-2 font-display text-3xl sm:text-4xl font-black text-slate-900">Nossos valores</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php $values = [
                ['t'=>'Transparência','d'=>'Preço que aparece é preço final. Contrato claro e objetivo.','i'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                ['t'=>'Agilidade','d'=>'Processos enxutos. Seu tempo vale mais que a burocracia.','i'=>'M13 10V3L4 14h7v7l9-11h-7z'],
                ['t'=>'Cuidado','d'=>'Cada carro passa por checagem completa a cada locação.','i'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
            ]; @endphp
            @foreach($values as $v)
                <div class="bg-white rounded-2xl p-8 border border-slate-100 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $v['i'] }}"/></svg>
                    </div>
                    <h3 class="font-display text-xl font-bold text-slate-900">{{ $v['t'] }}</h3>
                    <p class="mt-2 text-slate-500 leading-relaxed">{{ $v['d'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 to-slate-900 text-white p-10 sm:p-14 text-center">
            <h2 class="font-display text-3xl sm:text-4xl font-black">Pronto para começar?</h2>
            <p class="mt-3 text-primary-100">Explore nossa frota e encontre o carro ideal em minutos.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                <a href="{{ route('public.vehicles') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-white text-primary-700 hover:bg-slate-50 font-bold">Ver a frota</a>
                <a href="{{ route('public.contact') }}" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 font-bold">Fale conosco</a>
            </div>
        </div>
    </div>
</section>
@endsection
