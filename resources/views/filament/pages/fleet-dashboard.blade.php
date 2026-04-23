<x-filament-panels::page>
    {{-- ============================================================
         PAINEL EXECUTIVO — Elite Locadora
         Layout profissional: Hero · KPIs · Frota · Charts · Listas
         ============================================================ --}}

    @php
        $fmtMoney = fn ($v) => 'R$ ' . number_format((float) $v, 2, ',', '.');
        $mtd        = $financial['mtd_revenue']      ?? 0;
        $ytd        = $financial['ytd_revenue']      ?? 0;
        $overdue    = $financial['overdue_amount']   ?? 0;
        $overdueQty = $financial['overdue_count']    ?? 0;
        $open       = $financial['open_amount']      ?? 0;
        $activeCt   = $financial['active_contracts'] ?? 0;
        $pendRes    = $financial['pending_reserv']   ?? 0;
        $custs      = $financial['total_customers']  ?? 0;
    @endphp

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ============ HERO ============ --}}
        <div class="exec-hero">
            <div class="exec-hero-inner">
                <div>
                    <div class="exec-hero-title">Painel Executivo</div>
                    <div class="exec-hero-sub">
                        Visão consolidada de frota, contratos e financeiro —
                        <strong style="color:#cbd5e1;">{{ now()->translatedFormat('l, d \d\e F \d\e Y') }}</strong>
                    </div>
                    <span class="exec-hero-chip">
                        <span class="dot"></span>
                        Dados ao vivo · atualizado {{ $generatedAt->format('H:i') }}
                    </span>
                </div>
                <div class="exec-quick-actions">
                    <a href="{{ url('admin/contracts/create') }}" class="exec-action exec-action-orange">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Novo Contrato
                    </a>
                    <a href="{{ url('admin/reservations/create') }}" class="exec-action">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Reserva
                    </a>
                    <a href="{{ url('admin/vehicles/create') }}" class="exec-action">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13l2-5a2 2 0 012-1.5h10A2 2 0 0119 8l2 5m-18 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h12v1a1 1 0 001 1h1a1 1 0 001-1v-5m-18 0h18"/></svg>
                        Veículo
                    </a>
                    <a href="{{ url('admin/relatorios/lucratividade-frota') }}" class="exec-action">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 4 5-5"/></svg>
                        Relatórios
                    </a>
                </div>
            </div>
        </div>

        {{-- ============ KPIs PRIMÁRIOS ============ --}}
        <div class="exec-kpi-grid">
            {{-- Receita do Mês --}}
            <div class="exec-kpi accent-green">
                <div class="exec-kpi-head">
                    <div class="exec-kpi-label">Receita do Mês</div>
                    <span class="exec-kpi-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                </div>
                <div class="exec-kpi-value">{{ $fmtMoney($mtd) }}</div>
                <div class="exec-kpi-sub">Faturas pagas · {{ now()->translatedFormat('F') }}</div>
            </div>

            {{-- Contratos Ativos --}}
            <div class="exec-kpi accent-blue">
                <div class="exec-kpi-head">
                    <div class="exec-kpi-label">Contratos Ativos</div>
                    <span class="exec-kpi-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                </div>
                <div class="exec-kpi-value">{{ number_format($activeCt, 0, ',', '.') }}</div>
                <div class="exec-kpi-sub">{{ $pendRes }} reserva(s) pendente(s)</div>
            </div>

            {{-- Taxa de Utilização --}}
            <div class="exec-kpi {{ $utilizationRate >= 70 ? 'accent-green' : ($utilizationRate >= 40 ? 'accent-orange' : 'accent-red') }}">
                <div class="exec-kpi-head">
                    <div class="exec-kpi-label">Utilização da Frota</div>
                    <span class="exec-kpi-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </span>
                </div>
                <div class="exec-kpi-value">{{ $utilizationRate }}%</div>
                <div class="rpt-utilization-bar" style="margin-top:0.65rem;">
                    <div class="rpt-utilization-fill" style="width: {{ $utilizationRate }}%; background: {{ $utilizationRate >= 70 ? '#34d399' : ($utilizationRate >= 40 ? '#fb923c' : '#fb7185') }};"></div>
                </div>
                <div class="exec-kpi-sub">{{ $fleetCounts['rented'] }} de {{ $fleetCounts['total'] }} veículos em uso</div>
            </div>

            {{-- Inadimplência --}}
            <div class="exec-kpi {{ $overdueQty > 0 ? 'accent-red' : 'accent-green' }}">
                <div class="exec-kpi-head">
                    <div class="exec-kpi-label">Faturas em Atraso</div>
                    <span class="exec-kpi-icon">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.48 0L3.16 16.25A2 2 0 005 19z"/></svg>
                    </span>
                </div>
                <div class="exec-kpi-value {{ $overdue >= 100000 ? 'sm' : '' }}">{{ $fmtMoney($overdue) }}</div>
                <div class="exec-kpi-sub">{{ $overdueQty }} fatura(s) vencida(s) · Aberto: {{ $fmtMoney($open) }}</div>
            </div>
        </div>

        {{-- ============ STATUS DA FROTA (MINI) ============ --}}
        <div class="exec-chart-card" style="padding: 1rem 1.25rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem;">
                <div>
                    <h3 style="margin:0;">Status da Frota</h3>
                    <div class="exec-chart-sub" style="margin:0;">{{ $fleetCounts['total'] }} veículos · {{ $custs }} clientes ativos</div>
                </div>
                <a href="{{ url('admin/vehicles') }}" class="rpt-link" style="font-size:0.8rem;">Gerenciar frota →</a>
            </div>
            <div class="exec-mini-grid">
                <div class="exec-mini" style="color:#94a3b8;">
                    <div class="exec-mini-label">Total</div>
                    <div class="exec-mini-value" style="color:#e2e8f0;">{{ $fleetCounts['total'] }}</div>
                    <div class="exec-mini-sub">cadastrados</div>
                </div>
                <div class="exec-mini" style="color:#34d399;">
                    <div class="exec-mini-label">Disponíveis</div>
                    <div class="exec-mini-value">{{ $fleetCounts['available'] }}</div>
                    <div class="exec-mini-sub">prontos p/ locação</div>
                </div>
                <div class="exec-mini" style="color:#60a5fa;">
                    <div class="exec-mini-label">Locados</div>
                    <div class="exec-mini-value">{{ $fleetCounts['rented'] }}</div>
                    <div class="exec-mini-sub">com clientes</div>
                </div>
                <div class="exec-mini" style="color:#fb923c;">
                    <div class="exec-mini-label">Reservados</div>
                    <div class="exec-mini-value">{{ $fleetCounts['reserved'] }}</div>
                    <div class="exec-mini-sub">aguardando retirada</div>
                </div>
                <div class="exec-mini" style="color:#fbbf24;">
                    <div class="exec-mini-label">Manutenção</div>
                    <div class="exec-mini-value">{{ $fleetCounts['maintenance'] }}</div>
                    <div class="exec-mini-sub">oficina/inspeção</div>
                </div>
                <div class="exec-mini" style="color:#fb7185;">
                    <div class="exec-mini-label">Inativos</div>
                    <div class="exec-mini-value">{{ $fleetCounts['inactive'] }}</div>
                    <div class="exec-mini-sub">baixados/vendidos</div>
                </div>
            </div>
        </div>

        {{-- ============ GRÁFICOS ============ --}}
        <div class="exec-split">
            <div class="exec-chart-card">
                <h3>Receita Mensal · últimos 6 meses</h3>
                <div class="exec-chart-sub">Somente faturas com status <em>paga</em></div>
                <div class="exec-chart-wrap">
                    <canvas id="execRevenueChart"></canvas>
                </div>
            </div>

            <div class="exec-chart-card">
                <h3>Distribuição da Frota</h3>
                <div class="exec-chart-sub">Veículos por situação operacional</div>
                <div class="exec-chart-wrap sm">
                    <canvas id="execFleetChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ============ ALERTAS OPERACIONAIS ============ --}}
        <div class="rpt-grid rpt-grid-2">
            {{-- Manutenções --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>🔧 Manutenções Pendentes</h3>
                    <a href="{{ url('admin/maintenance-alerts') }}" class="rpt-link" style="font-size:0.8rem;">Ver todos →</a>
                </div>
                <table class="rpt-table">
                    <thead>
                        <tr><th>Veículo</th><th>Tipo</th><th>Vencimento</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pendingMaintenances as $alert)
                            @php
                                $isLate = ($alert->due_date && $alert->due_date->isPast())
                                    || ($alert->due_mileage && $alert->vehicle && $alert->vehicle->mileage >= $alert->due_mileage);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $alert->vehicle_id]) }}" class="rpt-link">
                                        <span class="rpt-badge-plate">{{ $alert->vehicle?->plate ?? '-' }}</span>
                                    </a>
                                    <div style="font-size:0.7rem;color:#6b7280;margin-top:0.15rem;">
                                        {{ $alert->vehicle?->brand }} {{ $alert->vehicle?->model }}
                                    </div>
                                </td>
                                <td style="color:#cbd5e1;">{{ $alert->type ?? '-' }}</td>
                                <td>
                                    <span class="rpt-badge {{ $isLate ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">
                                        @if($alert->due_date) {{ $alert->due_date->format('d/m/Y') }} @endif
                                        @if($alert->due_mileage) {{ number_format($alert->due_mileage, 0, ',', '.') }} km @endif
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="rpt-empty">Sem manutenções pendentes ✓</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Documentos --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>📄 Documentos Vencendo (30 dias)</h3>
                    <a href="{{ url('admin/vehicles') }}" class="rpt-link" style="font-size:0.8rem;">Ver veículos →</a>
                </div>
                <table class="rpt-table">
                    <thead>
                        <tr><th>Veículo</th><th>Documento</th><th>Vencimento</th></tr>
                    </thead>
                    <tbody>
                        @php $hasExpiring = false; $limit = now()->addDays(30); @endphp
                        @foreach($expiringDocs as $veh)
                            @foreach([
                                ['label' => 'IPVA',          'date' => $veh->ipva_due_date],
                                ['label' => 'Licenciamento', 'date' => $veh->licensing_due_date],
                                ['label' => 'Seguro',        'date' => $veh->insurance_expiry_date],
                            ] as $doc)
                                @if($doc['date'] && $doc['date'] <= $limit)
                                    @php $hasExpiring = true; @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="rpt-link">
                                                <span class="rpt-badge-plate">{{ $veh->plate }}</span>
                                            </a>
                                            <div style="font-size:0.7rem;color:#6b7280;margin-top:0.15rem;">{{ $veh->brand }} {{ $veh->model }}</div>
                                        </td>
                                        <td style="color:#cbd5e1;">{{ $doc['label'] }}</td>
                                        <td>
                                            <span class="rpt-badge {{ $doc['date']->isPast() ? 'rpt-badge-danger' : 'rpt-badge-warning' }}">
                                                {{ $doc['date']->format('d/m/Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                        @if(!$hasExpiring)
                            <tr><td colspan="3" class="rpt-empty">Documentação em dia ✓</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============ ATIVIDADE RECENTE ============ --}}
        <div class="rpt-grid rpt-grid-2">
            {{-- Contratos recentes --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>📝 Contratos Recentes</h3>
                    <a href="{{ url('admin/contracts') }}" class="rpt-link" style="font-size:0.8rem;">Todos os contratos →</a>
                </div>
                <div style="padding: 0.25rem 1.25rem 0.75rem;">
                    @forelse($recentContracts as $ct)
                        <div class="exec-activity-row">
                            <div class="exec-avatar">
                                {{ strtoupper(mb_substr($ct->customer?->name ?? 'C', 0, 2)) }}
                            </div>
                            <div class="exec-activity-main">
                                <div class="exec-activity-title">
                                    {{ $ct->customer?->name ?? 'Cliente' }}
                                    <span style="color:#64748b;font-weight:500;">· {{ $ct->contract_number ?? '#' . substr($ct->id, 0, 6) }}</span>
                                </div>
                                <div class="exec-activity-sub">
                                    {{ $ct->vehicle?->brand }} {{ $ct->vehicle?->model }}
                                    @if($ct->vehicle?->plate) · <span class="rpt-badge-plate" style="font-size:0.65rem;">{{ $ct->vehicle->plate }}</span> @endif
                                </div>
                            </div>
                            <div class="exec-activity-meta">
                                @php
                                    $statusClass = match(optional($ct->status)->value ?? $ct->status) {
                                        'ativo'                  => 'rpt-badge-success',
                                        'aguardando_assinatura'  => 'rpt-badge-warning',
                                        'cancelado'              => 'rpt-badge-danger',
                                        'finalizado'             => 'rpt-badge-info',
                                        default                  => 'rpt-badge-info',
                                    };
                                    $statusLabel = method_exists($ct->status, 'label') ? $ct->status->label() : ($ct->status ?? '-');
                                @endphp
                                <span class="rpt-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                <div style="margin-top:0.2rem;">{{ $ct->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="rpt-empty">Nenhum contrato registrado ainda.</div>
                    @endforelse
                </div>
            </div>

            {{-- Veículos recentes --}}
            <div class="rpt-section">
                <div class="rpt-section-header">
                    <h3>🚗 Veículos Recém-Cadastrados</h3>
                    <a href="{{ url('admin/vehicles') }}" class="rpt-link" style="font-size:0.8rem;">Todos →</a>
                </div>
                <div style="padding: 0.25rem 1.25rem 0.75rem;">
                    @forelse($recentVehicles as $veh)
                        <div class="exec-activity-row">
                            <div class="exec-avatar" style="background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.15));">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1.1rem;height:1.1rem;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13l2-5a2 2 0 012-1.5h10A2 2 0 0119 8l2 5m-18 0v5a1 1 0 001 1h1a1 1 0 001-1v-1h12v1a1 1 0 001 1h1a1 1 0 001-1v-5m-18 0h18"/></svg>
                            </div>
                            <div class="exec-activity-main">
                                <div class="exec-activity-title">
                                    <a href="{{ \App\Filament\Resources\VehicleResource::getUrl('view', ['record' => $veh->id]) }}" class="rpt-link">
                                        {{ $veh->brand }} {{ $veh->model }}
                                    </a>
                                    <span style="color:#64748b;font-weight:500;"> · {{ $veh->year ?? '—' }}</span>
                                </div>
                                <div class="exec-activity-sub">
                                    <span class="rpt-badge-plate" style="font-size:0.65rem;">{{ $veh->plate }}</span>
                                    @if($veh->category) · {{ $veh->category->name }} @endif
                                    @if($veh->branch) · {{ $veh->branch->name }} @endif
                                </div>
                            </div>
                            <div class="exec-activity-meta">
                                @php
                                    $vClass = match(optional($veh->status)->value ?? $veh->status) {
                                        'disponivel'  => 'rpt-badge-success',
                                        'locado'      => 'rpt-badge-info',
                                        'reservado'   => 'rpt-badge-warning',
                                        'manutencao'  => 'rpt-badge-warning',
                                        'inativo'     => 'rpt-badge-danger',
                                        default       => 'rpt-badge-info',
                                    };
                                @endphp
                                <span class="rpt-badge {{ $vClass }}">
                                    {{ method_exists($veh->status, 'getLabel') ? $veh->status->getLabel() : ($veh->status->value ?? $veh->status) }}
                                </span>
                                <div style="margin-top:0.2rem;">{{ $veh->created_at?->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="rpt-empty">Nenhum veículo cadastrado.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    {{-- ============ CHART.JS ============ --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const fleetData = @json([
                'labels' => ['Locados', 'Reservados', 'Disponíveis', 'Manutenção', 'Inativos'],
                'values' => [
                    (int) ($fleetCounts['rented']      ?? 0),
                    (int) ($fleetCounts['reserved']    ?? 0),
                    (int) ($fleetCounts['available']   ?? 0),
                    (int) ($fleetCounts['maintenance'] ?? 0),
                    (int) ($fleetCounts['inactive']    ?? 0),
                ],
                'colors' => ['#60a5fa', '#fb923c', '#34d399', '#fbbf24', '#fb7185'],
            ]);

            const revenueData = @json([
                'labels' => $revenueTrend['labels'] ?? [],
                'values' => $revenueTrend['values'] ?? [],
            ]);

            const brl = (v) => 'R$ ' + Number(v).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            const renderFleetChart = () => {
                const canvas = document.getElementById('execFleetChart');
                if (!canvas || typeof window.Chart === 'undefined') return;
                if (window.__execFleetChart) window.__execFleetChart.destroy();

                const filtered = fleetData.values
                    .map((v, i) => ({ label: fleetData.labels[i], value: v, color: fleetData.colors[i] }))
                    .filter(x => x.value > 0);
                const hasData = filtered.length > 0;

                window.__execFleetChart = new window.Chart(canvas, {
                    type: 'doughnut',
                    data: {
                        labels: hasData ? filtered.map(x => x.label) : ['Sem dados'],
                        datasets: [{
                            data: hasData ? filtered.map(x => x.value) : [1],
                            backgroundColor: hasData ? filtered.map(x => x.color) : ['#334155'],
                            borderColor: '#0b1120',
                            borderWidth: 3,
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '65%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#cbd5e1', boxWidth: 10, boxHeight: 10, padding: 10, font: { size: 11 } },
                            },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                borderColor: 'rgba(59,130,246,0.25)',
                                borderWidth: 1,
                                titleColor: '#f1f5f9',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                            },
                        },
                    },
                });
            };

            const renderRevenueChart = () => {
                const canvas = document.getElementById('execRevenueChart');
                if (!canvas || typeof window.Chart === 'undefined') return;
                if (window.__execRevenueChart) window.__execRevenueChart.destroy();

                const ctx = canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 280);
                gradient.addColorStop(0, 'rgba(37, 99, 235, 0.55)');
                gradient.addColorStop(1, 'rgba(37, 99, 235, 0.02)');

                window.__execRevenueChart = new window.Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: revenueData.labels,
                        datasets: [{
                            label: 'Receita',
                            data: revenueData.values,
                            backgroundColor: gradient,
                            borderColor: '#60a5fa',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            hoverBackgroundColor: '#3b82f6',
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#0f172a',
                                borderColor: 'rgba(59,130,246,0.25)',
                                borderWidth: 1,
                                titleColor: '#f1f5f9',
                                bodyColor: '#cbd5e1',
                                padding: 10,
                                callbacks: { label: (c) => brl(c.parsed.y) },
                            },
                        },
                        scales: {
                            x: {
                                grid: { display: false, drawBorder: false },
                                ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } },
                            },
                            y: {
                                grid: { color: 'rgba(148,163,184,0.08)', drawBorder: false },
                                ticks: {
                                    color: '#94a3b8',
                                    font: { size: 10 },
                                    callback: (v) => v >= 1000 ? 'R$ ' + (v / 1000).toFixed(0) + 'k' : 'R$ ' + v,
                                },
                                beginAtZero: true,
                            },
                        },
                    },
                });
            };

            const renderAll = () => { renderFleetChart(); renderRevenueChart(); };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderAll, { once: true });
            } else {
                renderAll();
            }
            document.addEventListener('livewire:navigated', renderAll);
        })();
    </script>
</x-filament-panels::page>
