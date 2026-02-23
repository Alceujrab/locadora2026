@extends('client.layouts.app')
@section('title', 'Suas Reservas')

@section('content')
<div class="sm:flex sm:items-center mb-8">
    <div class="sm:flex-auto">
        <h2 class="text-xl font-bold font-display text-gray-900">Histórico de Reservas</h2>
        <p class="mt-2 text-sm text-gray-500 font-medium">Acompanhe os agendamentos futuros e reservas antigas feitas conosco.</p>
    </div>
</div>

<div class="bg-white shadow-sm ring-1 ring-gray-200 rounded-2xl overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-bold text-gray-900 sm:pl-6">Local / Veículo</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Retirada</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Devolução</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-bold text-gray-900">Status</th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Ações</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
            @forelse($reservations as $reservation)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                        <div class="font-bold text-gray-900">{{ $reservation->vehicle->title ?? 'Veículo a definir' }}</div>
                        <div class="text-xs text-gray-500 font-medium mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $reservation->pickupBranch->name ?? 'Filial' }}
                        </div>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($reservation->pickup_date)->format('d/m/Y') }} <br>
                        <span class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($reservation->pickup_date)->format('H:i') }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-medium">
                        {{ \Carbon\Carbon::parse($reservation->return_date)->format('d/m/Y') }} <br>
                        <span class="text-xs text-gray-400 font-bold">{{ \Carbon\Carbon::parse($reservation->return_date)->format('H:i') }}</span>
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $reservation->status->color() === 'success' ? 'bg-green-50 text-green-700 ring-green-600/10' : ($reservation->status->color() === 'error' ? 'bg-red-50 text-red-700 ring-red-600/10' : 'bg-gray-50 text-gray-700 ring-gray-600/10') }}">
                            {{ $reservation->status->label() }}
                        </span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        @if($reservation->status->value === 'pendent' || $reservation->status->value === 'confirmed')
                            <a href="#" class="text-gray-400 font-bold hover:text-red-600 flex items-center justify-end gap-1 transition-colors" onclick="alert('Funcionalidade de Cancelamento pelo Cliente em desenvolvimento. Entre em contato com a locadora se deseja desfazer sua reserva.')">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Desfazer
                            </a>
                        @else
                            <span class="text-gray-300">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="py-12 px-4 text-center text-sm text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" />
                        </svg>
                        <p class="font-bold text-gray-900">Sem Histórico</p>
                        <p class="mt-1 font-medium">Você ainda não efetuou nenhuma reserva conosco.</p>
                        <a href="{{ route('public.vehicles') }}" class="mt-4 inline-flex font-bold text-primary-600 hover:text-primary-500 transition-colors">Faça sua primeira solicitação →</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
