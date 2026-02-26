<x-filament-panels::page>
    <style>
        /* ===== TABS ===== */
        .vd-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid rgba(255,255,255,0.06);
            margin-bottom: 1.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .vd-tab {
            padding: 0.75rem 1.25rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #6b7280;
            cursor: pointer;
            border: none;
            background: none;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            white-space: nowrap;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .vd-tab:hover { color: #d1d5db; }
        .vd-tab-active {
            color: #f59e0b !important;
            border-bottom-color: #f59e0b !important;
        }

        /* ===== HEADER ===== */
        .vd-header {
            background: linear-gradient(135deg, #111827 0%, #1e293b 100%);
            border-radius: 0.75rem;
            padding: 1.5rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .vd-header-photo {
            width: 9rem;
            height: 6.5rem;
            object-fit: cover;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
            flex-shrink: 0;
        }
        .vd-header-placeholder {
            width: 9rem;
            height: 6.5rem;
            background: #374151;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .vd-header-info { flex: 1; min-width: 0; }
        .vd-header-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
        }
        .vd-header-meta {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            font-size: 0.825rem;
            color: #d1d5db;
        }
        .vd-plate {
            background: #f59e0b;
            color: #000;
            font-weight: 700;
            padding: 0.125rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.825rem;
        }
        .vd-header-details {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .vd-status-area { text-align: right; flex-shrink: 0; }
        .vd-status-label {
            font-size: 0.7rem;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .vd-status-badge {
            display: inline-block;
            padding: 0.25rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }

        /* ===== CARDS ===== */
        .vd-grid { display: grid; gap: 1rem; }
        .vd-grid-4 { grid-template-columns: repeat(4, 1fr); }
        .vd-grid-5 { grid-template-columns: repeat(5, 1fr); }
        .vd-grid-3 { grid-template-columns: repeat(3, 1fr); }
        .vd-grid-2 { grid-template-columns: 1fr 1fr; }
        .vd-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.75rem;
            padding: 1.25rem;
        }
        .vd-card-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            font-weight: 600;
        }
        .vd-card-value {
            font-size: 1.5rem;
            font-weight: 800;
            margin-top: 0.25rem;
            line-height: 1.2;
        }
        .vd-card-sub {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* ===== COUNTERS ===== */
        .vd-counter {
            border-radius: 0.5rem;
            padding: 0.75rem;
            text-align: center;
        }
        .vd-counter-value {
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
        }
        .vd-counter-label {
            font-size: 0.7rem;
            color: #6b7280;
            margin-top: 0.125rem;
        }

        /* ===== SECTION ===== */
        .vd-section {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 0.75rem;
            overflow: hidden;
        }
        .vd-section-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .vd-section-header h3 {
            font-weight: 700;
            font-size: 0.95rem;
            margin: 0;
            color: #e5e7eb;
        }

        /* ===== TABLE ===== */
        .vd-table {
            width: 100%;
            font-size: 0.875rem;
            border-collapse: collapse;
        }
        .vd-table thead tr {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
        }
        .vd-table th, .vd-table td {
            padding: 0.625rem 1.25rem;
            text-align: left;
        }
        .vd-table tbody tr {
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .vd-table tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }
        .vd-badge {
            display: inline-block;
            padding: 0.125rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .vd-empty {
            padding: 2.5rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .vd-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(12rem, 1fr));
            gap: 0.75rem;
            padding: 1rem;
        }
        .vd-gallery img {
            width: 100%;
            height: 9rem;
            object-fit: cover;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .vd-gallery img:hover { transform: scale(1.03); }

        @media (max-width: 1024px) {
            .vd-grid-4 { grid-template-columns: repeat(2, 1fr); }
            .vd-grid-5 { grid-template-columns: repeat(3, 1fr); }
            .vd-grid-2 { grid-template-columns: 1fr; }
            .vd-header { flex-direction: column; align-items: flex-start; }
            .vd-status-area { text-align: left; }
        }
        @media (max-width: 640px) {
            .vd-grid-4, .vd-grid-5, .vd-grid-3 { grid-template-columns: repeat(2, 1fr); }
        }
    </style>

    <div x-data="{ activeTab: 'resumo' }">

        {{-- ========== HEADER DO VEICULO ========== --}}
        <div class="vd-header">
            @if($vehicle->cover_photo)
                <img src="{{ Storage::url($vehicle->cover_photo) }}" class="vd-header-photo" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" />
            @else
                <div class="vd-header-placeholder">
                    <x-heroicon-o-truck style="width: 3rem; height: 3rem; color: #6b7280;" />
                </div>
            @endif
            <div class="vd-header-info">
                <h2 class="vd-header-title">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                <div class="vd-header-meta">
                    <span class="vd-plate">{{ $vehicle->plate }}</span>
                    <span>{{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}</span>
                    @if($vehicle->color)<span>{{ $vehicle->color }}</span>@endif
                    @if($vehicle->category)<span>{{ $vehicle->category->name }}</span>@endif
                    @if($vehicle->branch)<span>{{ $vehicle->branch->name }}</span>@endif
                </div>
                <div class="vd-header-details">
                    <span>{{ number_format((float)$vehicle->mileage, 0, ',', '.') }} km</span>
                    @if($vehicle->fuel)<span>{{ $vehicle->fuel }}</span>@endif
                    @if($vehicle->transmission)<span>{{ $vehicle->transmission }}</span>@endif
                    @if($vehicle->insurance_expiry_date)
                        <span style="{{ $vehicle->insurance_expiry_date->isPast() ? 'color: #f87171;' : '' }}">Seguro: {{ $vehicle->insurance_expiry_date->format('d/m/Y') }}</span>
                    @endif
                    @if($vehicle->licensing_due_date)
                        <span style="{{ $vehicle->licensing_due_date->isPast() ? 'color: #f87171;' : '' }}">Licenc: {{ $vehicle->licensing_due_date->format('d/m/Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="vd-status-area">
                <div class="vd-status-label">Status</div>
                @php
                    $statusColors = match($vehicle->status->value) {
                        'disponivel' => 'background: rgba(34,197,94,0.2); color: #4ade80;',
                        'locado' => 'background: rgba(59,130,246,0.2); color: #60a5fa;',
                        'manutencao' => 'background: rgba(234,179,8,0.2); color: #facc15;',
                        'reservado' => 'background: rgba(99,102,241,0.2); color: #818cf8;',
                        'inativo' => 'background: rgba(239,68,68,0.2); color: #f87171;',
                        default => 'background: rgba(107,114,128,0.2); color: #9ca3af;',
                    };
                @endphp
                <span class="vd-status-badge" style="{{ $statusColors }}">
                    {{ $vehicle->status->label() }}
                </span>
            </div>
        </div>

        {{-- ========== NAVEGACAO POR ABAS ========== --}}
        <div class="vd-tabs">
            <button class="vd-tab" :class="activeTab === 'resumo' && 'vd-tab-active'" @click="activeTab = 'resumo'">
                📊 Resumo
            </button>
            <button class="vd-tab" :class="activeTab === 'locacoes' && 'vd-tab-active'" @click="activeTab = 'locacoes'">
                📋 Locacoes <span style="background: rgba(59,130,246,0.15); color: #60a5fa; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; margin-left: 0.25rem;">{{ $totalContracts }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'servicos' && 'vd-tab-active'" @click="activeTab = 'servicos'">
                🔧 Servicos <span style="background: rgba(234,88,12,0.15); color: #fb923c; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; margin-left: 0.25rem;">{{ $totalServiceOrders }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'financeiro' && 'vd-tab-active'" @click="activeTab = 'financeiro'">
                💰 Financeiro
            </button>
            @if($vehicle->photos->count() > 0)
                <button class="vd-tab" :class="activeTab === 'fotos' && 'vd-tab-active'" @click="activeTab = 'fotos'">
                    📸 Fotos <span style="background: rgba(167,139,250,0.15); color: #a78bfa; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; margin-left: 0.25rem;">{{ $vehicle->photos->count() }}</span>
                </button>
            @endif
        </div>

        {{-- ========== ABA: RESUMO ========== --}}
        <div x-show="activeTab === 'resumo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- KPIs --}}
            <div class="vd-grid vd-grid-4">
                <div class="vd-card">
                    <div class="vd-card-label">Receita Total</div>
                    <div class="vd-card-value" style="color: #4ade80;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
                    <div class="vd-card-sub">Contratos: R$ {{ number_format($revenueContracts, 2, ',', '.') }}</div>
                </div>
                <div class="vd-card">
                    <div class="vd-card-label">Despesas Total</div>
                    <div class="vd-card-value" style="color: #f87171;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</div>
                    <div class="vd-card-sub">OS: R$ {{ number_format($expensesOS, 2, ',', '.') }} | Multas: R$ {{ number_format($expensesFines, 2, ',', '.') }}</div>
                </div>
                <div class="vd-card">
                    <div class="vd-card-label">Lucro Liquido</div>
                    <div class="vd-card-value" style="color: {{ $profit >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                    <div class="vd-card-sub">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
                </div>
                <div class="vd-card">
                    <div class="vd-card-label">Diaria Media</div>
                    <div class="vd-card-value" style="color: #60a5fa;">R$ {{ number_format($avgDailyRate, 2, ',', '.') }}</div>
                    <div class="vd-card-sub">{{ $totalDaysRented }} dias locados</div>
                </div>
            </div>

            {{-- CONTADORES --}}
            <div class="vd-grid vd-grid-5">
                @php
                    $counters = [
                        ['value' => $totalContracts, 'label' => 'Contratos', 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.08)', 'border' => 'rgba(59,130,246,0.2)'],
                        ['value' => $totalReservations, 'label' => 'Reservas', 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,0.08)', 'border' => 'rgba(167,139,250,0.2)'],
                        ['value' => $totalServiceOrders, 'label' => 'Ordens de Servico', 'color' => '#fb923c', 'bg' => 'rgba(234,88,12,0.08)', 'border' => 'rgba(234,88,12,0.2)'],
                        ['value' => $totalFines, 'label' => 'Multas', 'color' => '#f87171', 'bg' => 'rgba(239,68,68,0.08)', 'border' => 'rgba(239,68,68,0.2)'],
                        ['value' => $totalInspections, 'label' => 'Vistorias', 'color' => '#c084fc', 'bg' => 'rgba(192,132,252,0.08)', 'border' => 'rgba(192,132,252,0.2)'],
                    ];
                @endphp
                @foreach($counters as $c)
                    <div class="vd-counter" style="background: {{ $c['bg'] }}; border: 1px solid {{ $c['border'] }};">
                        <div class="vd-counter-value" style="color: {{ $c['color'] }};">{{ $c['value'] }}</div>
                        <div class="vd-counter-label">{{ $c['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- CONTRATO ATIVO + MANUTENCAO --}}
            <div class="vd-grid vd-grid-2">
                @if($activeContract)
                    <div class="vd-card" style="border-color: rgba(59,130,246,0.25); background: rgba(59,130,246,0.04);">
                        <h3 style="font-weight: 700; color: #60a5fa; margin: 0 0 0.75rem 0; font-size: 0.95rem;">📋 Contrato Ativo</h3>
                        <div style="font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.375rem;">
                            <div><span style="color: #6b7280;">Cliente:</span> <strong>{{ $activeContract->customer?->name ?? '-' }}</strong></div>
                            <div><span style="color: #6b7280;">Periodo:</span> {{ $activeContract->pickup_date?->format('d/m/Y') ?? '-' }} a {{ $activeContract->return_date?->format('d/m/Y') ?? '-' }}</div>
                            <div><span style="color: #6b7280;">Valor:</span> <strong style="color: #4ade80;">R$ {{ number_format((float)($activeContract->total ?? 0), 2, ',', '.') }}</strong></div>
                        </div>
                    </div>
                @else
                    <div class="vd-card">
                        <h3 style="font-weight: 700; color: #6b7280; margin: 0 0 0.5rem 0; font-size: 0.95rem;">📋 Contrato Ativo</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Nenhum contrato ativo no momento.</p>
                    </div>
                @endif

                @if($nextMaintenance)
                    <div class="vd-card" style="border-color: rgba(234,179,8,0.25); background: rgba(234,179,8,0.04);">
                        <h3 style="font-weight: 700; color: #fbbf24; margin: 0 0 0.75rem 0; font-size: 0.95rem;">🔧 Proxima Manutencao</h3>
                        <div style="font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.375rem;">
                            <div><span style="color: #6b7280;">Tipo:</span> <strong>{{ $nextMaintenance->type ?? '-' }}</strong></div>
                            <div><span style="color: #6b7280;">Data:</span> {{ $nextMaintenance->due_date?->format('d/m/Y') ?? '-' }}</div>
                            <div><span style="color: #6b7280;">Descricao:</span> {{ $nextMaintenance->description ?? '-' }}</div>
                        </div>
                    </div>
                @else
                    <div class="vd-card">
                        <h3 style="font-weight: 700; color: #6b7280; margin: 0 0 0.5rem 0; font-size: 0.95rem;">🔧 Manutencao</h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Nenhuma manutencao pendente.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ========== ABA: LOCACOES ========== --}}
        <div x-show="activeTab === 'locacoes'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: flex; flex-direction: column; gap: 1.25rem;">

            <div class="vd-section">
                <div class="vd-section-header">
                    <h3>📊 Historico de Locacoes (Contratos)</h3>
                    <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalContracts }} contrato(s)</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="vd-table">
                        <thead>
                            <tr>
                                <th>Cliente</th>
                                <th>Periodo</th>
                                <th style="text-align: right;">Dias</th>
                                <th style="text-align: right;">Valor</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicle->contracts->sortByDesc('created_at') as $contract)
                                <tr>
                                    <td style="font-weight: 500;">{{ $contract->customer?->name ?? '-' }}</td>
                                    <td style="color: #9ca3af;">{{ $contract->pickup_date?->format('d/m') ?? '-' }} a {{ $contract->return_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td style="text-align: right;">{{ $contract->total_days ?? '-' }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #4ade80;">R$ {{ number_format((float)($contract->total ?? 0), 2, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        @php
                                            $cColor = ($contract->status?->value ?? '') === 'ativo'
                                                ? 'background: rgba(34,197,94,0.15); color: #4ade80;'
                                                : 'background: rgba(255,255,255,0.06); color: #9ca3af;';
                                        @endphp
                                        <span class="vd-badge" style="{{ $cColor }}">
                                            {{ $contract->status instanceof \BackedEnum ? $contract->status->label() : ($contract->status ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="vd-empty">Nenhum contrato registrado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- RESERVAS --}}
            @if($totalReservations > 0)
                <div class="vd-section">
                    <div class="vd-section-header">
                        <h3>📅 Reservas</h3>
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalReservations }} reserva(s)</span>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Periodo</th>
                                    <th style="text-align: right;">Valor</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->reservations->sortByDesc('created_at') as $res)
                                    <tr>
                                        <td style="font-weight: 500;">{{ $res->customer?->name ?? '-' }}</td>
                                        <td style="color: #9ca3af;">{{ $res->pickup_date?->format('d/m/Y') ?? '-' }} a {{ $res->return_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td style="text-align: right; font-weight: 600; color: #a78bfa;">R$ {{ number_format((float)($res->total ?? 0), 2, ',', '.') }}</td>
                                        <td style="text-align: center;">
                                            <span class="vd-badge" style="background: rgba(255,255,255,0.06); color: #9ca3af;">
                                                {{ $res->status instanceof \BackedEnum ? $res->status->label() : ($res->status ?? '-') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ========== ABA: SERVICOS ========== --}}
        <div x-show="activeTab === 'servicos'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- ORDENS DE SERVICO --}}
            <div class="vd-section">
                <div class="vd-section-header">
                    <h3>🔧 Ordens de Servico</h3>
                    <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalServiceOrders }} OS(s)</span>
                </div>
                <div style="overflow-x: auto;">
                    <table class="vd-table">
                        <thead>
                            <tr>
                                <th>OS #</th>
                                <th>Data</th>
                                <th>Descricao</th>
                                <th style="text-align: right;">Valor</th>
                                <th style="text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicle->serviceOrders->sortByDesc('created_at') as $os)
                                <tr>
                                    <td style="font-weight: 600; color: #e5e7eb;">#{{ $os->id }}</td>
                                    <td style="color: #9ca3af;">{{ $os->created_at?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($os->description ?? $os->notes ?? '-', 60) }}</td>
                                    <td style="text-align: right; font-weight: 600; color: #f87171;">R$ {{ number_format((float)($os->total ?? 0), 2, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        <span class="vd-badge" style="background: rgba(255,255,255,0.06); color: #9ca3af;">
                                            {{ $os->status instanceof \BackedEnum ? $os->status->label() : ($os->status ?? '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="vd-empty">Nenhuma OS registrada.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MULTAS --}}
            @if($totalFines > 0)
                <div class="vd-section">
                    <div class="vd-section-header">
                        <h3>⚠️ Multas de Transito</h3>
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalFines }} multa(s)</span>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Descricao</th>
                                    <th style="text-align: right;">Valor</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->fines->sortByDesc('infraction_date') as $fine)
                                    <tr>
                                        <td>{{ $fine->infraction_date?->format('d/m/Y') ?? '-' }}</td>
                                        <td>{{ $fine->description ?? '-' }}</td>
                                        <td style="text-align: right; font-weight: 600; color: #f87171;">R$ {{ number_format((float)($fine->amount ?? 0), 2, ',', '.') }}</td>
                                        <td style="text-align: center;">
                                            <span class="vd-badge" style="background: rgba(255,255,255,0.06); color: #9ca3af;">{{ $fine->status ?? '-' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- VISTORIAS --}}
            @if($totalInspections > 0)
                <div class="vd-section">
                    <div class="vd-section-header">
                        <h3>🔍 Vistorias</h3>
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalInspections }} vistoria(s)</span>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Tipo</th>
                                    <th>Observacao</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vehicle->inspections->sortByDesc('created_at') as $insp)
                                    <tr>
                                        <td>{{ $insp->created_at?->format('d/m/Y') ?? '-' }}</td>
                                        <td>
                                            <span class="vd-badge" style="background: rgba(192,132,252,0.15); color: #c084fc;">
                                                {{ $insp->type instanceof \BackedEnum ? $insp->type->label() : ($insp->type ?? '-') }}
                                            </span>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($insp->observations ?? '-', 60) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- ========== ABA: FINANCEIRO ========== --}}
        <div x-show="activeTab === 'financeiro'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: flex; flex-direction: column; gap: 1.25rem;">

            {{-- RESUMO FINANCEIRO DETALHADO --}}
            <div class="vd-grid vd-grid-2">
                {{-- RECEITAS --}}
                <div class="vd-section">
                    <div class="vd-section-header" style="background: rgba(34,197,94,0.04);">
                        <h3 style="color: #4ade80;">📈 Receitas</h3>
                        <span style="font-size: 1.1rem; font-weight: 800; color: #4ade80;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</span>
                    </div>
                    <div style="padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: 0.625rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                            <span style="color: #9ca3af;">Contratos ({{ $totalContracts }})</span>
                            <span style="font-weight: 600; color: #e5e7eb;">R$ {{ number_format($revenueContracts, 2, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                            <span style="color: #9ca3af;">Reservas ({{ $totalReservations }})</span>
                            <span style="font-weight: 600; color: #e5e7eb;">R$ {{ number_format($revenueReservations, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- DESPESAS --}}
                <div class="vd-section">
                    <div class="vd-section-header" style="background: rgba(239,68,68,0.04);">
                        <h3 style="color: #f87171;">📉 Despesas</h3>
                        <span style="font-size: 1.1rem; font-weight: 800; color: #f87171;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</span>
                    </div>
                    <div style="padding: 1rem 1.25rem; display: flex; flex-direction: column; gap: 0.625rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                            <span style="color: #9ca3af;">Ordens de Servico ({{ $totalServiceOrders }})</span>
                            <span style="font-weight: 600; color: #e5e7eb;">R$ {{ number_format($expensesOS, 2, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                            <span style="color: #9ca3af;">Multas ({{ $totalFines }})</span>
                            <span style="font-weight: 600; color: #e5e7eb;">R$ {{ number_format($expensesFines, 2, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                            <span style="color: #9ca3af;">Seguro</span>
                            <span style="font-weight: 600; color: #e5e7eb;">R$ {{ number_format($expensesInsurance, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RESULTADO --}}
            <div class="vd-card" style="border-color: {{ $profit >= 0 ? 'rgba(34,197,94,0.25)' : 'rgba(239,68,68,0.25)' }}; background: {{ $profit >= 0 ? 'rgba(34,197,94,0.04)' : 'rgba(239,68,68,0.04)' }};">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="font-size: 0.8rem; color: #9ca3af; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Resultado Liquido</div>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Receita - Despesas</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 2rem; font-weight: 800; color: {{ $profit >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                        <div style="font-size: 0.85rem; color: {{ $roi >= 0 ? '#4ade80' : '#f87171' }};">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
                    </div>
                </div>
            </div>

            {{-- VALORES DO VEICULO --}}
            <div class="vd-grid vd-grid-4">
                @php
                    $values = [
                        ['label' => 'Valor Compra', 'value' => $vehicle->purchase_value, 'color' => '#e5e7eb'],
                        ['label' => 'Valor FIPE', 'value' => $vehicle->fipe_value, 'color' => '#60a5fa'],
                        ['label' => 'Valor Seguro', 'value' => $vehicle->insurance_value, 'color' => '#fbbf24'],
                        ['label' => 'Diaria Configurada', 'value' => $vehicle->daily_rate, 'color' => '#4ade80'],
                    ];
                @endphp
                @foreach($values as $item)
                    <div class="vd-card" style="text-align: center;">
                        <div class="vd-card-label">{{ $item['label'] }}</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: {{ $item['color'] }}; margin-top: 0.375rem;">R$ {{ number_format((float)($item['value'] ?? 0), 2, ',', '.') }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========== ABA: FOTOS ========== --}}
        @if($vehicle->photos->count() > 0)
            <div x-show="activeTab === 'fotos'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="vd-section">
                    <div class="vd-section-header">
                        <h3>📸 Galeria de Fotos</h3>
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $vehicle->photos->count() }} foto(s)</span>
                    </div>
                    <div class="vd-gallery">
                        @foreach($vehicle->photos as $photo)
                            <img src="{{ Storage::url($photo->path) }}" alt="Foto #{{ $loop->iteration }}" loading="lazy" />
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-filament-panels::page>
