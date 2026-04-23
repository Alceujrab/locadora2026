<x-filament-panels::page>
    {{-- CSS classes loaded via custom-theme.blade.php --}}

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- CARDS DE STATUS --}}
        <div class="rpt-grid rpt-grid-6">
            @php
                $cards = [
                    ['label' => 'Total da Frota', 'value' => $fleetCounts['total'], 'color' => '#e2e8f0', 'bg' => 'rgba(59,130,246,0.04)', 'border' => 'rgba(59,130,246,0.12)', 'sub' => 'veiculos cadastrados'],
                    ['label' => 'Disponiveis', 'value' => $fleetCounts['available'], 'color' => '#34d399', 'bg' => 'rgba(16,185,129,0.06)', 'border' => 'rgba(16,185,129,0.2)', 'sub' => 'prontos para locacao'],
                    ['label' => 'Locados', 'value' => $fleetCounts['rented'], 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.06)', 'border' => 'rgba(59,130,246,0.2)', 'sub' => 'em posse de clientes'],
                    ['label' => 'Reservados', 'value' => $fleetCounts['reserved'] ?? 0, 'color' => '#fb923c', 'bg' => 'rgba(249,115,22,0.06)', 'border' => 'rgba(249,115,22,0.2)', 'sub' => 'aguardando retirada'],
                    ['label' => 'Manutencao', 'value' => $fleetCounts['maintenance'], 'color' => '#fbbf24', 'bg' => 'rgba(234,179,8,0.06)', 'border' => 'rgba(234,179,8,0.2)', 'sub' => 'oficina / inspecao'],
                    ['label' => 'Inativos', 'value' => $fleetCounts['inactive'], 'color' => '#fb7185', 'bg' => 'rgba(244,63,94,0.06)', 'border' => 'rgba(244,63,94,0.2)', 'sub' => 'vendidos / baixados'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="rpt-card" style="background: {{ $card['bg'] }}; border: 1px solid {{ $card['border'] }};">
                    <div class="rpt-card-label" style="color: {{ $card['color'] }};">{{ $card['label'] }}</div>
                    <div class="rpt-card-value" style="color: {{ $card['color'] }};">{{ $card['value'] }}</div>
                    <div class="rpt-card-sub">{{ $card['sub'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- TAXA DE UTILIZACAO --}}
        <div class="rpt-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 0.8rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">Taxa de Utilizacao da Frota</div>
                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Veiculos locados vs total da frota</div>
                </div>
                <div style="font-size: 2rem; font-weight: 800; letter-spacing: -0.025em; color: {{ $utilizationRate >= 70 ? '#34d399' : ($utilizationRate >= 40 ? '#fb923c' : '#fb7185') }};">
                    {{ $utilizationRate }}%
                </div>
            </div>
            <div class="rpt-utilization-bar">
                <div class="rpt-utilization-fill" style="width: {{ $utilizationRate }}%; background: {{ $utilizationRate >= 70 ? '#34d399' : ($utilizationRate >= 40 ? '#fb923c' : '#fb7185') }};"></div>
            </div>
            <div class="rpt-chart-container" style="padding: 1rem 0 0;">
                <div style="max-width: 230px; margin: 0 auto;">
                    <canvas id="fleetUtilizationChart" height="180"></canvas>
                </div>
            </div>
        </div>

        {{-- GRID 2 COLUNAS: MANUTENCAO + DOCUMENTOS --}}
        <div class="rpt-grid rpt-grid-2">

            {{-- ALERTAS DE MANUTENCAO --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>Alertas de Manutenção</h3>
                    <a href="{{ url('admin/maintenance-alerts') }}" class="rpt-link" style="font-size: 0.8rem;">Ver todos →</a>
                </div>
                <table class="rpt-table">
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
                                    <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $alert->vehicle_id]) }}" class="rpt-link">
                                        {{ $alert->vehicle?->plate ?? '-' }}
                                    </a>
                                    <div style="font-size: 0.7rem; color: #6b7280;">{{ $alert->vehicle?->brand }} {{ $alert->vehicle?->model }}</div>
                                </td>
                                <td>{{ $alert->type ?? '-' }}</td>
                                <td>
                                    <span class="rpt-badge {{ $isLate ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">
                                        @if($alert->due_date) {{ $alert->due_date->format('d/m/Y') }} @endif
                                        @if($alert->due_mileage) {{ number_format($alert->due_mileage, 0, ',', '.') }}km @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="rpt-empty">Nenhuma manutencao pendente ✓</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- DOCUMENTOS VENCENDO --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>Documentos Vencendo (30 dias)</h3>
                    <a href="{{ url('admin/vehicles') }}" class="rpt-link" style="font-size: 0.8rem;">Ir para Veiculos →</a>
                </div>
                <table class="rpt-table">
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
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="rpt-link">{{ $veh->plate }}</a></td>
                                    <td>IPVA</td>
                                    <td><span class="rpt-badge {{ $veh->ipva_due_date->isPast() ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">{{ $veh->ipva_due_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                            @if($veh->licensing_due_date && $veh->licensing_due_date <= now()->addDays(30))
                                @php $hasExpiring = true; @endphp
                                <tr>
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="rpt-link">{{ $veh->plate }}</a></td>
                                    <td>Licenciamento</td>
                                    <td><span class="rpt-badge {{ $veh->licensing_due_date->isPast() ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">{{ $veh->licensing_due_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                            @if($veh->insurance_expiry_date && $veh->insurance_expiry_date <= now()->addDays(30))
                                @php $hasExpiring = true; @endphp
                                <tr>
                                    <td><a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="rpt-link">{{ $veh->plate }}</a></td>
                                    <td>Seguro</td>
                                    <td><span class="rpt-badge {{ $veh->insurance_expiry_date->isPast() ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">{{ $veh->insurance_expiry_date->format('d/m/Y') }}</span></td>
                                </tr>
                            @endif
                        @endforeach
                        @if(!$hasExpiring)
                            <tr><td colspan="3" class="rpt-empty">Documentacao em dia ✓</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (() => {
            const chartData = @json([
                'labels' => ['Locados', 'Reservados', 'Disponiveis', 'Manutencao', 'Inativos'],
                'values' => [
                    (int) ($fleetCounts['rented'] ?? 0),
                    (int) ($fleetCounts['reserved'] ?? 0),
                    (int) ($fleetCounts['available'] ?? 0),
                    (int) ($fleetCounts['maintenance'] ?? 0),
                    (int) ($fleetCounts['inactive'] ?? 0),
                ],
                'colors' => ['#60a5fa', '#fb923c', '#34d399', '#fbbf24', '#fb7185'],
            ]);

            const renderFleetUtilizationChart = () => {
                const canvas = document.getElementById('fleetUtilizationChart');

                if (!canvas || typeof window.Chart === 'undefined') {
                    return;
                }

                if (window.fleetUtilizationChartInstance) {
                    window.fleetUtilizationChartInstance.destroy();
                }

                const filtered = chartData.values
                    .map((value, index) => ({
                        label: chartData.labels[index],
                        value,
                        color: chartData.colors[index],
                    }))
                    .filter((item) => item.value > 0);

                const labels = filtered.length ? filtered.map((item) => item.label) : ['Sem dados'];
                const values = filtered.length ? filtered.map((item) => item.value) : [1];
                const colors = filtered.length ? filtered.map((item) => item.color) : ['#64748b'];

                window.fleetUtilizationChartInstance = new window.Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: '#0f172a',
                            borderWidth: 2,
                            hoverOffset: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: '#cbd5e1',
                                    boxWidth: 12,
                                    boxHeight: 12,
                                    padding: 12,
                                },
                            },
                            tooltip: {
                                callbacks: {
                                    label: (context) => `${context.label}: ${context.parsed}`,
                                },
                            },
                        },
                    },
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderFleetUtilizationChart, { once: true });
            } else {
                renderFleetUtilizationChart();
            }

            document.addEventListener('livewire:navigated', renderFleetUtilizationChart);
        })();
    </script>
</x-filament-panels::page>
