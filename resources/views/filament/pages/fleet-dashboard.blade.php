<x-filament-panels::page>
    <div class="space-y-6">
        {{-- OVERVIEW CARDS --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="text-xs text-gray-500 uppercase font-bold">Total da Frota</div>
                <div class="text-3xl font-bold mt-1">{{ $fleetCounts['total'] }}</div>
                <div class="text-xs text-gray-400 mt-1">Veiculos cadastrados</div>
            </div>
            
            <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800 shadow-sm">
                <div class="text-xs text-green-600 dark:text-green-400 uppercase font-bold">Disponiveis</div>
                <div class="text-3xl font-bold text-green-700 dark:text-green-300 mt-1">{{ $fleetCounts['available'] }}</div>
                <div class="text-xs text-green-600/70 mt-1">Prontos para locacao</div>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800 shadow-sm">
                <div class="text-xs text-blue-600 dark:text-blue-400 uppercase font-bold">Alugados</div>
                <div class="text-3xl font-bold text-blue-700 dark:text-blue-300 mt-1">{{ $fleetCounts['rented'] }}</div>
                <div class="text-xs text-blue-600/70 mt-1">Em posse de clientes</div>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 border border-yellow-200 dark:border-yellow-800 shadow-sm">
                <div class="text-xs text-yellow-600 dark:text-yellow-400 uppercase font-bold">Em Manutencao</div>
                <div class="text-3xl font-bold text-yellow-700 dark:text-yellow-300 mt-1">{{ $fleetCounts['maintenance'] }}</div>
                <div class="text-xs text-yellow-600/70 mt-1">Oficina / Inspeção</div>
            </div>

            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 border border-red-200 dark:border-red-800 shadow-sm">
                <div class="text-xs text-red-600 dark:text-red-400 uppercase font-bold">Inativos</div>
                <div class="text-3xl font-bold text-red-700 dark:text-red-300 mt-1">{{ $fleetCounts['inactive'] }}</div>
                <div class="text-xs text-red-600/70 mt-1">Vendidos / PT / Furtados</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- ALERTAS DE MANUTENÇÃO --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 dark:text-gray-300">🔧 Alertas de Manutencao (Proximos envios)</h3>
                    <a href="{{ url('admin/maintenance-alerts') }}" class="text-sm text-primary-600 hover:underline">Ver todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">Veiculo</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Vencimento (Data/KM)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($pendingMaintenances as $alert)
                            @php
                                $isLate = ($alert->due_date && $alert->due_date->isPast()) || ($alert->due_mileage && $alert->vehicle && $alert->vehicle->mileage >= $alert->due_mileage);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-2 font-medium">
                                    <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $alert->vehicle_id]) }}" class="text-primary-600 hover:underline">
                                        {{ $alert->vehicle?->plate }} - {{ $alert->vehicle?->brand }} {{ $alert->vehicle?->model }}
                                    </a>
                                </td>
                                <td class="px-4 py-2">{{ $alert->type }}</td>
                                <td class="px-4 py-2 {{ $isLate ? 'text-red-600 font-bold' : 'text-gray-600' }}">
                                    @if($alert->due_date) {{ $alert->due_date->format('d/m/Y') }} @endif
                                    @if($alert->due_date && $alert->due_mileage) / @endif
                                    @if($alert->due_mileage) {{ number_format($alert->due_mileage, 0, ',', '.') }} km @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Frota em dia! Nenhuma manutencao pendente. 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- DOCUMENTOS VENCENDO --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="bg-gray-50 dark:bg-gray-900 px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700 dark:text-gray-300">📋 Documentos Vencendo (30 dias)</h3>
                    <a href="{{ url('admin/vehicles') }}" class="text-sm text-primary-600 hover:underline">Ir para Veiculos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900 text-xs text-gray-500 uppercase">
                            <tr>
                                <th class="px-4 py-2 text-left">Veiculo</th>
                                <th class="px-4 py-2 text-left">Documento</th>
                                <th class="px-4 py-2 text-left">Vencimento</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($expiringDocs as $veh)
                                @if($veh->ipva_due_date && $veh->ipva_due_date <= now()->addDays(30))
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 font-medium">
                                        <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="text-primary-600 hover:underline">
                                            {{ $veh->plate }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">IPVA</td>
                                    <td class="px-4 py-2 {{ $veh->ipva_due_date->isPast() ? 'text-red-600 font-bold' : 'text-orange-500' }}">{{ $veh->ipva_due_date->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($veh->licensing_due_date && $veh->licensing_due_date <= now()->addDays(30))
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 font-medium">
                                        <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="text-primary-600 hover:underline">
                                            {{ $veh->plate }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">Licenciamento</td>
                                    <td class="px-4 py-2 {{ $veh->licensing_due_date->isPast() ? 'text-red-600 font-bold' : 'text-orange-500' }}">{{ $veh->licensing_due_date->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                                @if($veh->insurance_expiry_date && $veh->insurance_expiry_date <= now()->addDays(30))
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 font-medium">
                                        <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="text-primary-600 hover:underline">
                                            {{ $veh->plate }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-2">Seguro</td>
                                    <td class="px-4 py-2 {{ $veh->insurance_expiry_date->isPast() ? 'text-red-600 font-bold' : 'text-orange-500' }}">{{ $veh->insurance_expiry_date->format('d/m/Y') }}</td>
                                </tr>
                                @endif
                            @empty
                            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Documentacao da frota totalmente em dia. 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
