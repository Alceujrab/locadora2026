@extends('client.layouts.app')
@section('title', 'Help Desk / Chamados')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-semibold font-display text-slate-900">Meus Chamados</h2>
        <p class="mt-2 text-sm text-slate-500">Abra solicitações de assistência técnica, dúvidas de faturamento ou emergências.</p>
    </div>
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <button type="button" class="block rounded-xl bg-purple-600 px-4 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600" onclick="alert('Formulário de Abertura de Chamado em desenvolvimento.')">
            Novo Chamado
        </button>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-slate-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 sm:pl-6">Protocolo</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Abertura</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Assunto</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Prioridade</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-slate-900">Status</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Ações</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            @forelse($tickets as $ticket)
                @php
                    $statusColor = match($ticket->status) {
                        'aberto' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        'em_andamento' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                        'resolvido' => 'bg-green-50 text-green-700 ring-green-600/20',
                        'fechado' => 'bg-slate-50 text-slate-700 ring-slate-600/20',
                        default => 'bg-slate-50 text-slate-700 ring-slate-600/20',
                    };
                    $statusName = match($ticket->status) {
                        'aberto' => 'Aguardando',
                        'em_andamento' => 'Em Atendimento',
                        'resolvido' => 'Resolvido',
                        'fechado' => 'Encerrado',
                        default => $ticket->status,
                    };
                    
                    $priorityColor = match($ticket->priority) {
                        'baixa' => 'text-slate-500',
                        'media' => 'text-amber-600',
                        'alta' => 'text-orange-600 font-medium',
                        'urgente' => 'text-red-600 font-bold flex bg-red-50 p-1 px-2 rounded w-fit',
                        default => 'text-slate-500',
                    };
                    $priorityName = match($ticket->priority) {
                        'baixa' => 'Baixa',
                        'media' => 'Média',
                        'alta' => 'Alta',
                        'urgente' => 'Urgente!',
                        default => $ticket->priority,
                    };
                @endphp
                
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 sm:pl-6 leading-tigth">
                        #{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}<br>
                        <span class="text-xs text-slate-400 capitalize">{{ $ticket->category }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500">
                        {{ \Carbon\Carbon::parse($ticket->created_at)->format('d/m/Y') }}
                    </td>
                    <td class="px-3 py-4 text-sm text-slate-900 max-w-xs truncate" title="{{ $ticket->subject }}">
                        {{ $ticket->subject }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-xs tracking-wide uppercase {{ $priorityColor }}">
                        @if($ticket->priority === 'urgente')
                            <svg class="w-4 h-4 mr-1 inline" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        @endif
                        {{ $priorityName }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $statusColor }}">
                            {{ $statusName }}
                        </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <a href="#" class="text-purple-600 hover:text-purple-900" onclick="alert('Área de Chat/Mensagens em desenvolvimento.')">Ver Diálogo</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 px-4 text-center text-sm text-slate-500">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <p class="font-medium text-slate-900">Nenhum Chamado</p>
                        <p class="mt-1">Você não tem histórico de suporte no sistema.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
