<x-filament-panels::page>
    <div class="space-y-6">
        {{-- OVERVIEW CARDS --}}
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem;">
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase; font-weight: 700;">Total da Frota</div>
                <div style="font-size: 1.875rem; font-weight: 700; margin-top: 0.25rem;">{{ $fleetCounts['total'] }}</div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">Veiculos cadastrados</div>
            </div>

            <div style="background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.25); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.75rem; color: #16a34a; text-transform: uppercase; font-weight: 700;">Disponiveis</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: #15803d; margin-top: 0.25rem;">{{ $fleetCounts['available'] }}</div>
                <div style="font-size: 0.75rem; color: rgba(22,163,74,0.7); margin-top: 0.25rem;">Prontos para locacao</div>
            </div>

            <div style="background: rgba(59,130,246,0.08); border: 1px solid rgba(59,130,246,0.25); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.75rem; color: #2563eb; text-transform: uppercase; font-weight: 700;">Alugados</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: #1d4ed8; margin-top: 0.25rem;">{{ $fleetCounts['rented'] }}</div>
                <div style="font-size: 0.75rem; color: rgba(37,99,235,0.7); margin-top: 0.25rem;">Em posse de clientes</div>
            </div>

            <div style="background: rgba(234,179,8,0.08); border: 1px solid rgba(234,179,8,0.25); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.75rem; color: #ca8a04; text-transform: uppercase; font-weight: 700;">Em Manutencao</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: #a16207; margin-top: 0.25rem;">{{ $fleetCounts['maintenance'] }}</div>
                <div style="font-size: 0.75rem; color: rgba(202,138,4,0.7); margin-top: 0.25rem;">Oficina / Inspecao</div>
            </div>

            <div style="background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.25); border-radius: 0.75rem; padding: 1rem;">
                <div style="font-size: 0.75rem; color: #dc2626; text-transform: uppercase; font-weight: 700;">Inativos</div>
                <div style="font-size: 1.875rem; font-weight: 700; color: #b91c1c; margin-top: 0.25rem;">{{ $fleetCounts['inactive'] }}</div>
                <div style="font-size: 0.75rem; color: rgba(220,38,38,0.7); margin-top: 0.25rem;">Vendidos / PT / Furtados</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            {{-- ALERTAS DE MANUTENCAO --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
                <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-weight: 700; color: #374151; margin: 0;">🔧 Alertas de Manutencao (Proximos envios)</h3>
                    <a href="{{ url('admin/maintenance-alerts') }}" style="font-size: 0.875rem; color: #f59e0b; text-decoration: none;">Ver todos</a>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.02); font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">
                                <th style="padding: 0.5rem 1rem; text-align: left;">Veiculo</th>
                                <th style="padding: 0.5rem 1rem; text-align: left;">Tipo</th>
                                <th style="padding: 0.5rem 1rem; text-align: left;">Vencimento (Data/KM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingMaintenances as $alert)
                                @php
                                    $isLate = ($alert->due_date && $alert->due_date->isPast()) || ($alert->due_mileage && $alert->vehicle && $alert->vehicle->mileage >= $alert->due_mileage);
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 0.5rem 1rem; font-weight: 500;">
                                        <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $alert->vehicle_id]) }}" style="color: #f59e0b; text-decoration: none;">
                                            {{ $alert->vehicle?->plate }} - {{ $alert->vehicle?->brand }} {{ $alert->vehicle?->model }}
                                        </a>
                                    </td>
                                    <td style="padding: 0.5rem 1rem;">{{ $alert->type }}</td>
                                    <td style="padding: 0.5rem 1rem; {{ $isLate ? 'color: #dc2626; font-weight: 700;' : 'color: #4b5563;' }}">
                                        @if($alert->due_date) {{ $alert->due_date->format('d/m/Y') }} @endif
                                        @if($alert->due_date && $alert->due_mileage) / @endif
                                        @if($alert->due_mileage) {{ number_format($alert->due_mileage, 0, ',', '.') }} km @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" style="padding: 1.5rem; text-align: center; color: #9ca3af;">Frota em dia! Nenhuma manutencao pendente. 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- DOCUMENTOS VENCENDO --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
                <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06); display: flex; justify-content: space-between; align-items: center;">
                    <h3 style="font-weight: 700; color: #374151; margin: 0;">📋 Documentos Vencendo (30 dias)</h3>
                    <a href="{{ url('admin/vehicles') }}" style="font-size: 0.875rem; color: #f59e0b; text-decoration: none;">Ir para Veiculos</a>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.02); font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">
                                <th style="padding: 0.5rem 1rem; text-align: left;">Veiculo</th>
                                <th style="padding: 0.5rem 1rem; text-align: left;">Documento</th>
                                <th style="padding: 0.5rem 1rem; text-align: left;">Vencimento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expiringDocs as $veh)
                                @if($veh->ipva_due_date && $veh->ipva_due_date <= now()->addDays(30))
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <td style="padding: 0.5rem 1rem; font-weight: 500;">
                                            <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" style="color: #f59e0b; text-decoration: none;">
                                                {{ $veh->plate }}
                                            </a>
                                        </td>
                                        <td style="padding: 0.5rem 1rem;">IPVA</td>
                                        <td style="padding: 0.5rem 1rem; {{ $veh->ipva_due_date->isPast() ? 'color: #dc2626; font-weight: 700;' : 'color: #ea580c;' }}">{{ $veh->ipva_due_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                                @if($veh->licensing_due_date && $veh->licensing_due_date <= now()->addDays(30))
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <td style="padding: 0.5rem 1rem; font-weight: 500;">
                                            <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" style="color: #f59e0b; text-decoration: none;">
                                                {{ $veh->plate }}
                                            </a>
                                        </td>
                                        <td style="padding: 0.5rem 1rem;">Licenciamento</td>
                                        <td style="padding: 0.5rem 1rem; {{ $veh->licensing_due_date->isPast() ? 'color: #dc2626; font-weight: 700;' : 'color: #ea580c;' }}">{{ $veh->licensing_due_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                                @if($veh->insurance_expiry_date && $veh->insurance_expiry_date <= now()->addDays(30))
                                    <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <td style="padding: 0.5rem 1rem; font-weight: 500;">
                                            <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" style="color: #f59e0b; text-decoration: none;">
                                                {{ $veh->plate }}
                                            </a>
                                        </td>
                                        <td style="padding: 0.5rem 1rem;">Seguro</td>
                                        <td style="padding: 0.5rem 1rem; {{ $veh->insurance_expiry_date->isPast() ? 'color: #dc2626; font-weight: 700;' : 'color: #ea580c;' }}">{{ $veh->insurance_expiry_date->format('d/m/Y') }}</td>
                                    </tr>
                                @endif
                            @empty
                                <tr><td colspan="3" style="padding: 1.5rem; text-align: center; color: #9ca3af;">Documentacao da frota totalmente em dia. 🎉</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
