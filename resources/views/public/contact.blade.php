@extends('public.layouts.app')
@section('title', 'Contato')

@section('content')
<section class="bg-slate-950 text-white py-20 relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-br from-primary-900/40 via-slate-950 to-slate-950"></div>
    <div class="absolute -top-24 right-0 w-96 h-96 bg-primary-500/20 rounded-full blur-3xl"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-block px-3 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold uppercase tracking-widest text-primary-300">Fale Conosco</span>
        <h1 class="mt-6 font-display text-4xl sm:text-5xl font-black tracking-tight">Estamos prontos para atender você.</h1>
        <p class="mt-4 max-w-xl mx-auto text-slate-300">Dúvidas, reservas corporativas ou suporte: escolha o canal mais rápido para você.</p>
    </div>
</section>

<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-4">
            @if($whatsappLink)
                <a href="{{ $whatsappLink }}" target="_blank" rel="noopener" class="block bg-emerald-50 border border-emerald-100 rounded-2xl p-6 hover:shadow-xl transition group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448L.057 24zM6.597 20.193c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.888-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-emerald-900">WhatsApp</p>
                            <p class="text-sm text-emerald-700 mt-1">{{ $companyWhatsapp ?? 'Fale com a gente em tempo real' }}</p>
                        </div>
                    </div>
                </a>
            @endif

            @if($companyPhone)
                <a href="tel:{{ preg_replace('/\D/', '', $companyPhone) }}" class="block bg-slate-50 border border-slate-100 rounded-2xl p-6 hover:shadow-xl transition group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Telefone</p>
                            <p class="text-sm text-slate-500 mt-1">{{ $companyPhone }}</p>
                        </div>
                    </div>
                </a>
            @endif

            @if($companyEmail)
                <a href="mailto:{{ $companyEmail }}" class="block bg-slate-50 border border-slate-100 rounded-2xl p-6 hover:shadow-xl transition group">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-600 text-white flex items-center justify-center shrink-0 group-hover:scale-110 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">E-mail</p>
                            <p class="text-sm text-slate-500 mt-1 break-all">{{ $companyEmail }}</p>
                        </div>
                    </div>
                </a>
            @endif

            @if($companyAddress)
                <div class="bg-slate-50 border border-slate-100 rounded-2xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-800 text-white flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">Endereço</p>
                            <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ $companyAddress }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl ring-1 ring-slate-100 shadow-sm p-8 sm:p-10">
                <h2 class="font-display text-2xl font-black text-slate-900">Envie uma mensagem</h2>
                <p class="text-sm text-slate-500 mt-1">Retornamos em até 1 dia útil.</p>

                @if($errors->any())
                    <div class="mt-6 bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('public.contact.submit') }}" class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @csrf
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nome completo *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-2">E-mail *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Telefone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Assunto</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mensagem *</label>
                        <textarea name="message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-primary-500 focus:ring-2 focus:ring-primary-500">{{ old('message') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" class="inline-flex items-center gap-2 px-7 py-4 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-bold shadow-lg shadow-primary-600/30 transition">
                            Enviar mensagem
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
