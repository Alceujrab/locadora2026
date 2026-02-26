<x-filament-panels::page>
    <style>
        .fleet-grid { display: grid; gap: 1rem; }
        .fleet-grid-6 { grid-template-columns: repeat(6, 1fr); }
        .fleet-grid-2 { grid-template-columns: 1fr 1fr; }
        .fleet-card {
            border-radius: 0.75rem;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }
        .fleet-card-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            opacity: 0.7;
        }
        .fleet-card-value {
            font-size: 2rem;
            font-weight: 800;
            line-height: 1.2;
            margin-top: 0.25rem;
        }
        .fleet-card-sub {
            font-size: 0.75rem;
            opacity: 0.5;
            margin-top: 0.25rem;
        }
        .fleet-section {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .fleet-section-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .fleet-section-header h3 {
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
            color: #e5e7eb;
        }
        .fleet-table {
            width: 100%;
            font-size: 0.875rem;
            border-collapse: collapse;
        }
        .fleet-table thead tr {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }
        .fleet-table th, .fleet-table td {
            padding: 0.625rem 1.25rem;
            text-align: left;
        }
        .fleet-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .fleet-table tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }
        .fleet-link {
            color: #f59e0b;
            text-decoration: none;
            font-weight: 500;
        }
        .fleet-link:hover { text-decoration: underline; }
        .fleet-badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .fleet-badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
        .fleet-badge-warning { background: rgba(234,179,8,0.15); color: #fbbf24; }
        .fleet-empty {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .fleet-utilization-bar {
            height: 0.5rem;
            border-radius: 9999px;
            background: rgba(255,255,255,0.06);
            overflow: hidden;
            margin-top: 0.75rem;
        }
        .fleet-utilization-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s ease;
        }
        @media (max-width: 1024px) {
            .fleet-grid-6 { grid-template-columns: repeat(3, 1fr); }
            .fleet-grid-2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .fleet-grid-6 { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- CARDS DE STATUS --}}
        <div class="fleet-grid fleet-grid-6">
            @php
                $cards = [
                    ['label' => 'Total da Frota', 'value' => $fleetCounts['total'], 'color' => '#e5e7eb', 'bg' => 'rgba(255,255,255,0.04)', 'border' => 'rgba(255,255,255,0.08)', 'sub' => 'veiculos cadastrados'],
                    ['label' => 'Disponiveis', 'value' => $fleetCounts['available'], 'color' => '#4ade80', 'bg' => 'rgba(34,197,94,0.06)', 'border' => 'rgba(34,197,94,0.2)', 'sub' => 'prontos para locacao'],
                    ['label' => 'Locados', 'value' => $fleetCounts['rented'], 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.06)', 'border' => 'rgba(59,130,246,0.2)', 'sub' => 'em posse de clientes'],
                    ['label' => 'Reservados', 'value' => $fleetCounts['reserved'] ?? 0, 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,0.06)', 'border' => 'rgba(167,139,250,0.2)', 'sub' => 'aguardando retirada'],
                    ['label' => 'Manutencao', 'value' => $fleetCounts['maintenance'], 'color' => '#fbbf24', 'bg' => 'rgba(234,179,8,0.06)', 'border' => 'rgba(234,179,8,0.2)', 'sub' => 'oficina / inspecao'],
                    ['label' => 'Inativos', 'value' => $fleetCounts['inactive'], 'color' => '#f87171', 'bg' => 'rgba(239,68,68,0.06)', 'border' => 'rgba(239,68,68,0.2)', 'sub' => 'vendidos / baixados'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="fleet-card" style="background: {{ $card['bg'] }}; border: 1px solid {{ $card['border'] }};">
                    <div class="fleet-card-label" style="color: {{ $card['color'] }};">{{ $card['label'] }}</div>
                    <div class="fleet-card-value" style="color: {{ $card['color'] }};">{{ $card['value'] }}</div>
                    <div class="fleet-card-sub">{{ $card['sub'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- TAXA DE UTILIZACAO --}}
        <div class="fleet-card" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.8rem; color: #9ca3af; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Taxa de Utilizacao da Frota</div>
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Veiculos locados vs total da frota</div>
                </div>
                <div style="font-size: 2rem; font-weight: 800; color: {{ $utilizationRate >= 70 ? '#4ade80' : ($utilizationRate >= 40 ? '#fbbf24' : '#f87171') }};">
                    {{ $utilizationRate }}%
                </div>
            </div>
            <div class="fleet-utilization-bar">
                <div class="fleet-utilization-fill" style="width: {{ $utilizationRate }}%; background: {{ $utilizationRate >= 70 ? '#4ade80' : ($utilizationRate >= 40 ? '#fbbf24' : '#f87171') }};"></div>
            </div>
        </div>

        {{-- GRID 2 COLUNAS: MANUTENCAO + DOCUMENTOS --}}
        <div class="fleet-grid fleet-grid-2">

            {{-- ALERTAS DE MANUTENCAO --}}
            <div class="fleet-section">
                <div class="fleet-section-header">
                    <h3>🔧 Alertas de Manutencao</h3>
                    <a href="{{ url('admin/maintenance-alerts') }}" class="fleet-link" style="font-size: 0.8rem;">Ver todos →</a>
                </div>
                <table class="fleet-table">
                    <thead>
                        <tr>
                            <th>Veiculo</th>
                            <th>Tipo</th>
                            <th>Vencimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingMaintenances as $alert)
                            @php
                                $isLate = ($alert->due_date && $alert->due_date->isPast()) || ($alert->due_mileage && $alert->vehicle && $alert->vehicle->mileage >= $alert->due_mileage);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $alert->vehicle_id]) }}" class="fleet-link">
                                        {{ $alert->vehicle?->plate ?? '-' }}
                                    </a>
                                    <div style="font-size: 0.7rem; color: #6b7280;">{{ $alert->vehicle?->brand }} {{ $alert->vehicle?->model }}</div>
                                </td>
                                <td>{{ $alert->type ?? '-' }}</td>
                                <td>
                                    <span class="fleet-badge {{ $isLate ? 'fleet-badge-danger' : 'fleet-badge-warning' }}">
                                        @if($alert->due_date) {{ $alert->due_date->format('d/m/Y') }} @endif
                                        @if($alert->due_mileage) {{ number_format($alert->due_mileage, 0, ',', '.') }}km @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="fleet-empty">Nenhuma manutencao pendente ✓</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- DOCUMENTOS VENCENDO --}}
            <div class="fleet-section">
                <div class="fleet-section-header">
                    <h3>📋 Documentos Vencendo (30 dias)</h3>
                    <a href="{{ url('admin/vehicles') }}" class="fleet-link" style="font-size: 0.8rem;">Ir para Veiculos →</a>
                </div>
                <table class="fleet-table">
                    <thead>
                        <tr>
                            <th>Veiculo</th>
                            <th>Documento</th>
                            <th>Vencimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $hasExpiring = false; @endphp
                        @foreach($expiringDocs as $veh)
                            @if($veh->ipva_due_date && $veh->ipva_due_date <= now()->addDays(30))
                                @php $hasExpiring = true; @endphp
                                <tr>
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="fleet-link">{{ $veh->plate }}</a></td>
                                    <td>IPVA</td>
                                    <td><span class="fleet-badge {{ $veh->ipva_due_date->isPast() ? 'fleet-badge-danger' : 'fleet-badge-warning' }}">{{ $veh->ipva_due_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                            @if($veh->licensing_due_date && $veh->licensing_due_date <= now()->addDays(30))
                                @php $hasExpiring = true; @endphp
                                <tr>
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="fleet-link">{{ $veh->plate }}</a></td>
                                    <td>Licenciamento</td>
                                    <td><span class="fleet-badge {{ $veh->licensing_due_date->isPast() ? 'fleet-badge-danger' : 'fleet-badge-warning' }}">{{ $veh->licensing_due_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                            @if($veh->insurance_expiry_date && $veh->insurance_expiry_date <= now()->addDays(30))
                                @php $hasExpiring = true; @endphp
                                <tr>
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="fleet-link">{{ $veh->plate }}</a></td>
                                    <td>Seguro</td>
                                    <td><span class="fleet-badge {{ $veh->insurance_expiry_date->isPast() ? 'fleet-badge-danger' : 'fleet-badge-warning' }}">{{ $veh->insurance_expiry_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                        @endforeach
                        @if(!$hasExpiring)
                            <tr><td colspan="3" class="fleet-empty">Documentacao em dia ✓</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-filament-panels::page>
