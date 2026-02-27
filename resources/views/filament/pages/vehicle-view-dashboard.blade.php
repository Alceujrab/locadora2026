<x-filament-panels::page>
    <style>
        /* === Layout Grids (responsivo) === */
        .vd-grid { display: grid; gap: 1.5rem; }
        .vd-g4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .vd-g5 { grid-template-columns: repeat(5, minmax(0, 1fr)); }
        .vd-g2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }

        /* === Tab Navigation === */
        .vd-tabs { display: flex; border-bottom: 2px solid rgba(255,255,255,0.1); overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .vd-tab {
            padding: 0.75rem 1.25rem; font-size: 0.875rem; font-weight: 600; color: #6b7280;
            cursor: pointer; border: none; background: none; border-bottom: 2px solid transparent;
            margin-bottom: -2px; white-space: nowrap; transition: all 0.15s;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .vd-tab:hover { color: #d1d5db; }
        .vd-tab-active { color: #f59e0b !important; border-bottom-color: #f59e0b !important; }

        /* === Tabela === */
        .vd-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .vd-table thead th {
            padding: 0.75rem 1.5rem; text-align: left; font-size: 0.75rem;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;
        }
        .vd-table tbody td { padding: 0.75rem 1.5rem; }
        .vd-table tbody tr { border-bottom: 1px solid rgba(255,255,255,0.05); }
        .vd-table tbody tr:hover { background: rgba(255,255,255,0.02); }

        /* === Utilitarios === */
        .vd-badge { display: inline-flex; align-items: center; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600; }
        .vd-empty { padding: 2.5rem; text-align: center; color: #6b7280; font-size: 0.875rem; }
        .vd-tab-badge { padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; margin-left: 0.25rem; }

        /* === Galeria === */
        .vd-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(12rem, 1fr)); gap: 0.75rem; padding: 1.5rem; }
        .vd-gallery img { width: 100%; height: 9rem; object-fit: cover; border-radius: 0.5rem; cursor: pointer; transition: transform 0.15s; }
        .vd-gallery img:hover { transform: scale(1.03); }

        /* === Responsivo === */
        @media (max-width: 1024px) {
            .vd-g4, .vd-g5 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .vd-g2 { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .vd-g4, .vd-g5 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>

    <div x-data="{ activeTab: 'resumo' }" style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- ========== HEADER DO VEICULO ========== --}}
        <div style="background: linear-gradient(135deg, #111827 0%, #1e293b 100%); border-radius: 0.75rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05); outline: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
            <div style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem;">
                @if($vehicle->cover_photo)
                    <img src="{{ Storage::url($vehicle->cover_photo) }}"
                         style="width: 10rem; height: 7rem; object-fit: cover; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.4); flex-shrink: 0;"
                         alt="{{ $vehicle->brand }} {{ $vehicle->model }}" />
                @else
                    <div style="width: 10rem; height: 7rem; background: #374151; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <x-heroicon-o-truck style="width: 3rem; height: 3rem; color: #6b7280;" />
                    </div>
                @endif
                <div style="flex: 1; min-width: 0;">
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff; margin: 0; line-height: 1.2;">
                        {{ $vehicle->brand }} {{ $vehicle->model }}
                    </h2>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem; font-size: 0.825rem; color: #d1d5db; align-items: center;">
                        <span style="background: #f59e0b; color: #000; font-weight: 700; padding: 0.125rem 0.75rem; border-radius: 0.25rem; font-size: 0.825rem;">{{ $vehicle->plate }}</span>
                        <span>{{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}</span>
                        @if($vehicle->color)<span>{{ $vehicle->color }}</span>@endif
                        @if($vehicle->category)<span>{{ $vehicle->category->name }}</span>@endif
                        @if($vehicle->branch)<span>{{ $vehicle->branch->name }}</span>@endif
                    </div>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.5rem; font-size: 0.75rem; color: #9ca3af;">
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
                <div style="text-align: right; flex-shrink: 0;">
                    <div style="font-size: 0.7rem; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em;">Status</div>
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
                    <span style="{{ $statusColors }} display: inline-block; padding: 0.25rem 0.85rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 700; margin-top: 0.25rem;">
                        {{ $vehicle->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- ========== TABS ========== --}}
        <div class="vd-tabs">
            <button class="vd-tab" :class="activeTab === 'resumo' && 'vd-tab-active'" @click="activeTab = 'resumo'">
                Resumo
            </button>
            <button class="vd-tab" :class="activeTab === 'locacoes' && 'vd-tab-active'" @click="activeTab = 'locacoes'">
                Locacoes <span class="vd-tab-badge" style="background: rgba(59,130,246,0.15); color: #60a5fa;">{{ $totalContracts }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'servicos' && 'vd-tab-active'" @click="activeTab = 'servicos'">
                Servicos <span class="vd-tab-badge" style="background: rgba(234,88,12,0.15); color: #fb923c;">{{ $totalServiceOrders }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'financeiro' && 'vd-tab-active'" @click="activeTab = 'financeiro'">
                Financeiro
            </button>
            @if($vehicle->photos->count() > 0)
                <button class="vd-tab" :class="activeTab === 'fotos' && 'vd-tab-active'" @click="activeTab = 'fotos'">
                    Fotos <span class="vd-tab-badge" style="background: rgba(167,139,250,0.15); color: #a78bfa;">{{ $vehicle->photos->count() }}</span>
                </button>
            @endif
        </div>

        {{-- ========== ABA: RESUMO ========== --}}
        <div x-show="activeTab === 'resumo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- KPIs (usando fi-wi-stats-overview-stat = identico ao widget nativo Filament) --}}
            <div class="vd-grid vd-g4">
                <div class="fi-wi-stats-overview-stat">
                    <div class="fi-wi-stats-overview-stat-content">
                        <div class="fi-wi-stats-overview-stat-label-ctn">
                            <span class="fi-wi-stats-overview-stat-label">Receita Total</span>
                        </div>
                        <div class="fi-wi-stats-overview-stat-value" style="color: #4ade80;">
                            R$ {{ number_format($totalRevenue, 2, ',', '.') }}
                        </div>
                        <div class="fi-wi-stats-overview-stat-description">
                            <span>Contratos: R$ {{ number_format($revenueContracts, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="fi-wi-stats-overview-stat">
                    <div class="fi-wi-stats-overview-stat-content">
                        <div class="fi-wi-stats-overview-stat-label-ctn">
                            <span class="fi-wi-stats-overview-stat-label">Despesas Total</span>
                        </div>
                        <div class="fi-wi-stats-overview-stat-value" style="color: #f87171;">
                            R$ {{ number_format($totalExpenses, 2, ',', '.') }}
                        </div>
                        <div class="fi-wi-stats-overview-stat-description">
                            <span>OS: R$ {{ number_format($expensesOS, 2, ',', '.') }} | Multas: R$ {{ number_format($expensesFines, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="fi-wi-stats-overview-stat">
                    <div class="fi-wi-stats-overview-stat-content">
                        <div class="fi-wi-stats-overview-stat-label-ctn">
                            <span class="fi-wi-stats-overview-stat-label">Lucro Liquido</span>
                        </div>
                        <div class="fi-wi-stats-overview-stat-value" style="color: {{ $profit >= 0 ? '#4ade80' : '#f87171' }};">
                            R$ {{ number_format($profit, 2, ',', '.') }}
                        </div>
                        <div class="fi-wi-stats-overview-stat-description">
                            <span>ROI: {{ number_format($roi, 1, ',', '.') }}%</span>
                        </div>
                    </div>
                </div>

                <div class="fi-wi-stats-overview-stat">
                    <div class="fi-wi-stats-overview-stat-content">
                        <div class="fi-wi-stats-overview-stat-label-ctn">
                            <span class="fi-wi-stats-overview-stat-label">Diaria Media</span>
                        </div>
                        <div class="fi-wi-stats-overview-stat-value" style="color: #60a5fa;">
                            R$ {{ number_format($avgDailyRate, 2, ',', '.') }}
                        </div>
                        <div class="fi-wi-stats-overview-stat-description">
                            <span>{{ $totalDaysRented }} dias locados</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CONTADORES --}}
            <div class="vd-grid vd-g5">
                @php
                    $counters = [
                        ['value' => $totalContracts, 'label' => 'Contratos', 'color' => '#60a5fa', 'bg' => 'rgba(59,130,246,0.06)', 'ring' => 'rgba(59,130,246,0.2)'],
                        ['value' => $totalReservations, 'label' => 'Reservas', 'color' => '#a78bfa', 'bg' => 'rgba(167,139,250,0.06)', 'ring' => 'rgba(167,139,250,0.2)'],
                        ['value' => $totalServiceOrders, 'label' => 'Ordens de Servico', 'color' => '#fb923c', 'bg' => 'rgba(234,88,12,0.06)', 'ring' => 'rgba(234,88,12,0.2)'],
                        ['value' => $totalFines, 'label' => 'Multas', 'color' => '#f87171', 'bg' => 'rgba(239,68,68,0.06)', 'ring' => 'rgba(239,68,68,0.2)'],
                        ['value' => $totalInspections, 'label' => 'Vistorias', 'color' => '#c084fc', 'bg' => 'rgba(192,132,252,0.06)', 'ring' => 'rgba(192,132,252,0.2)'],
                    ];
                @endphp
                @foreach($counters as $c)
                    <div style="text-align: center; padding: 1.25rem 1rem; border-radius: 0.75rem; background: {{ $c['bg'] }}; border: 1px solid {{ $c['ring'] }};">
                        <div style="font-size: 1.75rem; font-weight: 800; color: {{ $c['color'] }}; line-height: 1.2;">{{ $c['value'] }}</div>
                        <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.375rem;">{{ $c['label'] }}</div>
                    </div>
                @endforeach
            </div>

            {{-- CONTRATO ATIVO + MANUTENCAO (usando fi-section nativo) --}}
            <div class="vd-grid vd-g2">
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Contrato Ativo</h3>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            @if($activeContract)
                                <div style="font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div><span style="color: #6b7280;">Cliente:</span> <strong>{{ $activeContract->customer?->name ?? '-' }}</strong></div>
                                    <div><span style="color: #6b7280;">Periodo:</span> {{ $activeContract->pickup_date?->format('d/m/Y') ?? '-' }} a {{ $activeContract->return_date?->format('d/m/Y') ?? '-' }}</div>
                                    <div><span style="color: #6b7280;">Valor:</span> <strong style="color: #4ade80;">R$ {{ number_format((float)($activeContract->total ?? 0), 2, ',', '.') }}</strong></div>
                                </div>
                            @else
                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Nenhum contrato ativo no momento.</p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Manutencao</h3>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            @if($nextMaintenance)
                                <div style="font-size: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div><span style="color: #6b7280;">Tipo:</span> <strong>{{ $nextMaintenance->type ?? '-' }}</strong></div>
                                    <div><span style="color: #6b7280;">Data:</span> {{ $nextMaintenance->due_date?->format('d/m/Y') ?? '-' }}</div>
                                    <div><span style="color: #6b7280;">Descricao:</span> {{ $nextMaintenance->description ?? '-' }}</div>
                                </div>
                            @else
                                <p style="font-size: 0.875rem; color: #6b7280; margin: 0;">Nenhuma manutencao pendente.</p>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- ========== ABA: LOCACOES ========== --}}
        <div x-show="activeTab === 'locacoes'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            <section class="fi-section fi-section-has-header">
                <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="fi-section-header-text-ctn">
                        <h3 class="fi-section-header-heading">Historico de Locacoes (Contratos)</h3>
                    </div>
                    <div class="fi-section-header-after-ctn">
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalContracts }} contrato(s)</span>
                    </div>
                </header>
                <div class="fi-section-content-ctn">
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
            </section>

            @if($totalReservations > 0)
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Reservas</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalReservations }} reserva(s)</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
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
                </section>
            @endif
        </div>

        {{-- ========== ABA: SERVICOS ========== --}}
        <div x-show="activeTab === 'servicos'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            <section class="fi-section fi-section-has-header">
                <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div class="fi-section-header-text-ctn">
                        <h3 class="fi-section-header-heading">Ordens de Servico</h3>
                    </div>
                    <div class="fi-section-header-after-ctn">
                        <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalServiceOrders }} OS(s)</span>
                    </div>
                </header>
                <div class="fi-section-content-ctn">
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
            </section>

            @if($totalFines > 0)
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Multas de Transito</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalFines }} multa(s)</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
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
                </section>
            @endif

            @if($totalInspections > 0)
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Vistorias</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 0.8rem; color: #6b7280;">{{ $totalInspections }} vistoria(s)</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
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
                </section>
            @endif
        </div>

        {{-- ========== ABA: FINANCEIRO ========== --}}
        <div x-show="activeTab === 'financeiro'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- RECEITAS + DESPESAS --}}
            <div class="vd-grid vd-g2">
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center; background: rgba(34,197,94,0.04);">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading" style="color: #4ade80;">Receitas</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 1.1rem; font-weight: 800; color: #4ade80;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
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
                    </div>
                </section>

                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center; background: rgba(239,68,68,0.04);">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading" style="color: #f87171;">Despesas</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 1.1rem; font-weight: 800; color: #f87171;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            <div style="display: flex; flex-direction: column; gap: 0.625rem;">
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
                </section>
            </div>

            {{-- RESULTADO --}}
            <div class="fi-wi-stats-overview-stat" style="{{ $profit >= 0 ? 'outline: 1px solid rgba(34,197,94,0.25); background: rgba(34,197,94,0.03);' : 'outline: 1px solid rgba(239,68,68,0.25); background: rgba(239,68,68,0.03);' }}">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="fi-wi-stats-overview-stat-label">Resultado Liquido</div>
                        <div class="fi-wi-stats-overview-stat-description" style="margin-top: 0.25rem;">
                            <span>Receita - Despesas</span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 2rem; font-weight: 800; color: {{ $profit >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                        <div style="font-size: 0.85rem; color: {{ $roi >= 0 ? '#4ade80' : '#f87171' }};">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
                    </div>
                </div>
            </div>

            {{-- VALORES DO VEICULO --}}
            <div class="vd-grid vd-g4">
                @php
                    $values = [
                        ['label' => 'Valor Compra', 'value' => $vehicle->purchase_value, 'color' => '#e5e7eb'],
                        ['label' => 'Valor FIPE', 'value' => $vehicle->fipe_value, 'color' => '#60a5fa'],
                        ['label' => 'Valor Seguro', 'value' => $vehicle->insurance_value, 'color' => '#fbbf24'],
                        ['label' => 'Diaria Configurada', 'value' => $vehicle->daily_rate, 'color' => '#4ade80'],
                    ];
                @endphp
                @foreach($values as $item)
                    <div class="fi-wi-stats-overview-stat" style="text-align: center;">
                        <div class="fi-wi-stats-overview-stat-content">
                            <div class="fi-wi-stats-overview-stat-label-ctn" style="justify-content: center;">
                                <span class="fi-wi-stats-overview-stat-label">{{ $item['label'] }}</span>
                            </div>
                            <div class="fi-wi-stats-overview-stat-value" style="color: {{ $item['color'] }}; font-size: 1.25rem; text-align: center;">
                                R$ {{ number_format((float)($item['value'] ?? 0), 2, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ========== ABA: FOTOS ========== --}}
        @if($vehicle->photos->count() > 0)
            <div x-show="activeTab === 'fotos'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Galeria de Fotos</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 0.8rem; color: #6b7280;">{{ $vehicle->photos->count() }} foto(s)</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="vd-gallery">
                            @foreach($vehicle->photos as $photo)
                                <img src="{{ Storage::url($photo->path) }}" alt="Foto #{{ $loop->iteration }}" loading="lazy" />
                            @endforeach
                        </div>
                    </div>
                </section>
            </div>
        @endif

    </div>
</x-filament-panels::page>
