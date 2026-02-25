<x-filament-panels::page>
<div class="space-y-6">
    {{-- HEADER: Dados do Veículo --}}
    <div class="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-6 text-white flex items-center gap-6">
        @if($vehicle->cover_photo)
        <img src="{{ Storage::url($vehicle->cover_photo) }}" class="w-32 h-24 object-cover rounded-lg shadow-lg" />
        @else
        <div class="w-32 h-24 bg-gray-700 rounded-lg flex items-center justify-center">
            <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25m-4.5 0V5.625m0 12.75h-2.25m2.25 0H9.75"/></svg>
        </div>
        @endif
        <div class="flex-1">
            <h2 class="text-2xl font-bold">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
            <div class="flex gap-4 mt-1 text-sm text-gray-300">
                <span class="bg-amber-500 text-black font-bold px-3 py-0.5 rounded">{{ $vehicle->plate }}</span>
                <span>{{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}</span>
                <span>{{ $vehicle->color }}</span>
                <span>{{ $vehicle->category?->name }}</span>
                <span>{{ $vehicle->branch?->name }}</span>
            </div>
            <div class="flex gap-4 mt-2 text-xs text-gray-400">
                <span>🛣️ {{ number_format((float)$vehicle->mileage, 0, ',', '.') }} km</span>
                <span>⛽ {{ $vehicle->fuel }}</span>
                <span>⚙️ {{ $vehicle->transmission }}</span>
                @if($vehicle->insurance_expiry_date)
                <span class="{{ $vehicle->insurance_expiry_date->isPast() ? 'text-red-400' : '' }}">🛡️ Seguro: {{ $vehicle->insurance_expiry_date->format('d/m/Y') }}</span>
                @endif
                @if($vehicle->licensing_due_date)
                <span class="{{ $vehicle->licensing_due_date->isPast() ? 'text-red-400' : '' }}">📋 Licenc: {{ $vehicle->licensing_due_date->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
        <div class="text-right">
            <div class="text-xs text-gray-400 uppercase">Status</div>
            <span class="px-3 py-1 rounded-full text-sm font-bold
                {{ $vehicle->status->value === 'disponivel' ? 'bg-green-500/20 text-green-400' : '' }}
                {{ $vehicle->status->value === 'alugado' ? 'bg-blue-500/20 text-blue-400' : '' }}
                {{ $vehicle->status->value === 'manutencao' ? 'bg-yellow-500/20 text-yellow-400' : '' }}
                {{ $vehicle->status->value === 'inativo' ? 'bg-red-500/20 text-red-400' : '' }}
            ">{{ $vehicle->status->getLabel() }}</span>
        </div>
    </div>

    {{-- KPIs FINANCEIROS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase">Receita Total</div>
            <div class="text-2xl font-bold text-green-600">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">Contratos: R$ {{ number_format($revenueContracts, 2, ',', '.') }} | Reservas: R$ {{ number_format($revenueReservations, 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase">Despesas Total</div>
            <div class="text-2xl font-bold text-red-600">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">OS: R$ {{ number_format($expensesOS, 2, ',', '.') }} | Multas: R$ {{ number_format($expensesFines, 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase">Lucro Liquido</div>
            <div class="text-2xl font-bold {{ $profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">R$ {{ number_format($profit, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 uppercase">Diaria Media</div>
            <div class="text-2xl font-bold text-blue-600">R$ {{ number_format($avgDailyRate, 2, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ $totalDaysRented }} dias locados</div>
        </div>
    </div>

    {{-- CONTADORES --}}
    <div class="grid grid-cols-3 md:grid-cols-5 gap-3">
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center border border-blue-200 dark:border-blue-800">
            <div class="text-3xl font-bold text-blue-600">{{ $totalContracts }}</div>
            <div class="text-xs text-gray-500">Contratos</div>
        </div>
        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 text-center border border-indigo-200 dark:border-indigo-800">
            <div class="text-3xl font-bold text-indigo-600">{{ $totalReservations }}</div>
            <div class="text-xs text-gray-500">Reservas</div>
        </div>
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-3 text-center border border-orange-200 dark:border-orange-800">
            <div class="text-3xl font-bold text-orange-600">{{ $totalServiceOrders }}</div>
            <div class="text-xs text-gray-500">Ordens de Servico</div>
        </div>
        <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-3 text-center border border-red-200 dark:border-red-800">
            <div class="text-3xl font-bold text-red-600">{{ $totalFines }}</div>
            <div class="text-xs text-gray-500">Multas</div>
        </div>
        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-3 text-center border border-purple-200 dark:border-purple-800">
            <div class="text-3xl font-bold text-purple-600">{{ $totalInspections }}</div>
            <div class="text-xs text-gray-500">Vistorias</div>
        </div>
    </div>

    {{-- CONTRATO ATIVO + MANUTENÇÃO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($activeContract)
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-5 border border-blue-200 dark:border-blue-800">
            <h3 class="font-bold text-blue-800 dark:text-blue-400 mb-2">📋 Contrato Ativo</h3>
            <div class="space-y-1 text-sm">
                <div><strong>Cliente:</strong> {{ $activeContract->customer?->name }}</div>
                <div><strong>Periodo:</strong> {{ $activeContract->pickup_date?->format('d/m/Y') }} a {{ $activeContract->return_date?->format('d/m/Y') }}</div>
                <div><strong>Valor:</strong> R$ {{ number_format((float)$activeContract->total, 2, ',', '.') }}</div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-600 dark:text-gray-400 mb-2">📋 Contrato Ativo</h3>
            <p class="text-sm text-gray-500">Nenhum contrato ativo no momento.</p>
        </div>
        @endif

        @if($nextMaintenance)
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-5 border border-yellow-200 dark:border-yellow-800">
            <h3 class="font-bold text-yellow-800 dark:text-yellow-400 mb-2">🔧 Proxima Manutencao</h3>
            <div class="space-y-1 text-sm">
                <div><strong>Tipo:</strong> {{ $nextMaintenance->type ?? '-' }}</div>
                <div><strong>Data:</strong> {{ $nextMaintenance->due_date?->format('d/m/Y') ?? '-' }}</div>
                <div><strong>Descricao:</strong> {{ $nextMaintenance->description ?? '-' }}</div>
            </div>
        </div>
        @else
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-600 dark:text-gray-400 mb-2">🔧 Manutencao</h3>
            <p class="text-sm text-gray-500">Nenhuma manutencao pendente.</p>
        </div>
        @endif
    </div>

    {{-- HISTÓRICO DE LOCAÇÕES --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-300">📊 Historico de Locacoes (Contratos)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Cliente</th>
                        <th class="px-4 py-2 text-left">Periodo</th>
                        <th class="px-4 py-2 text-right">Dias</th>
                        <th class="px-4 py-2 text-right">Valor</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($vehicle->contracts->sortByDesc('created_at')->take(10) as $contract)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 font-medium">{{ $contract->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $contract->pickup_date?->format('d/m') }} a {{ $contract->return_date?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-right">{{ $contract->total_days ?? '-' }}</td>
                        <td class="px-4 py-2 text-right font-medium text-green-600">R$ {{ number_format((float)$contract->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs
                                {{ $contract->status?->value === 'ativo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}
                            ">{{ $contract->status?->getLabel() ?? $contract->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Nenhum contrato registrado.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ORDENS DE SERVIÇO --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-300">🔧 Ordens de Servico</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">OS #</th>
                        <th class="px-4 py-2 text-left">Data</th>
                        <th class="px-4 py-2 text-left">Descricao</th>
                        <th class="px-4 py-2 text-right">Valor</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($vehicle->serviceOrders->sortByDesc('created_at')->take(10) as $os)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2 font-medium">#{{ $os->id }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $os->created_at?->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($os->description ?? $os->notes ?? '-', 50) }}</td>
                        <td class="px-4 py-2 text-right font-medium text-red-600">R$ {{ number_format((float)$os->total, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $os->status?->getLabel() ?? $os->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">Nenhuma OS registrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MULTAS --}}
    @if($totalFines > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-700">
            <h3 class="font-bold text-gray-700 dark:text-gray-300">⚠️ Multas de Transito</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Data</th>
                        <th class="px-4 py-2 text-left">Descricao</th>
                        <th class="px-4 py-2 text-right">Valor</th>
                        <th class="px-4 py-2 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($vehicle->fines->sortByDesc('infraction_date')->take(10) as $fine)
                    <tr>
                        <td class="px-4 py-2">{{ $fine->infraction_date?->format('d/m/Y') ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $fine->description ?? '-' }}</td>
                        <td class="px-4 py-2 text-right font-medium text-red-600">R$ {{ number_format((float)$fine->amount, 2, ',', '.') }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">{{ $fine->status ?? '-' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- VALORES DO VEÍCULO --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-xs text-gray-500">Valor Compra</div>
            <div class="text-lg font-bold">R$ {{ number_format((float)$vehicle->purchase_value, 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-xs text-gray-500">Valor FIPE</div>
            <div class="text-lg font-bold">R$ {{ number_format((float)$vehicle->fipe_value, 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-xs text-gray-500">Valor Seguro</div>
            <div class="text-lg font-bold">R$ {{ number_format((float)$vehicle->insurance_value, 2, ',', '.') }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 text-center">
            <div class="text-xs text-gray-500">Diaria Configurada</div>
            <div class="text-lg font-bold">R$ {{ number_format($vehicle->daily_rate, 2, ',', '.') }}</div>
        </div>
    </div>
</div>
</x-filament-panels::page>
