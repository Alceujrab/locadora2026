<x-filament-panels::page>
    {{-- CSS classes loaded via custom-theme.blade.php --}}

    <div x-data="{ activeTab: 'resumo' }" style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- ========== HEADER DO VEICULO ========== --}}
        <div class="vd-header">
            @if($vehicle->cover_photo)
                <img src="{{ Storage::url($vehicle->cover_photo) }}" class="vd-header-photo"
                     alt="{{ $vehicle->brand }} {{ $vehicle->model }}" />
            @else
                <div class="vd-header-placeholder">
                    <x-heroicon-o-truck style="width: 2.5rem; height: 2.5rem; color: #6b7280;" />
                </div>
            @endif
            <div style="flex: 1; min-width: 0;">
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
                        'disponivel' => 'background: rgba(34,197,94,0.15); color: #4ade80;',
                        'locado' => 'background: rgba(59,130,246,0.15); color: #60a5fa;',
                        'manutencao' => 'background: rgba(234,179,8,0.15); color: #facc15;',
                        'reservado' => 'background: rgba(99,102,241,0.15); color: #818cf8;',
                        'inativo' => 'background: rgba(239,68,68,0.15); color: #f87171;',
                        default => 'background: rgba(107,114,128,0.15); color: #9ca3af;',
                    };
                @endphp
                <span class="vd-status-badge" style="{{ $statusColors }}">
                    {{ $vehicle->status->label() }}
                </span>
                <div style="margin-top: 0.5rem;">
                    <a href="{{ route('admin.vehicle.report.pdf', $vehicle->id) }}"
                       style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.4rem 0.85rem;
                              background:rgba(239,68,68,0.12);color:#f87171;border:1px solid rgba(239,68,68,0.2);
                              border-radius:0.4rem;font-size:0.75rem;font-weight:600;text-decoration:none;
                              transition:opacity 0.15s;"
                       onmouseover="this.style.opacity=0.8" onmouseout="this.style.opacity=1">
                        Relatório PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ========== TABS ========== --}}
        <div class="vd-tabs">
            <button class="vd-tab" :class="activeTab === 'resumo' && 'vd-tab-active'" @click="activeTab = 'resumo'">
                Resumo
            </button>
            <button class="vd-tab" :class="activeTab === 'locacoes' && 'vd-tab-active'" @click="activeTab = 'locacoes'">
                Locações <span class="vd-tab-badge" style="background: rgba(59,130,246,0.15); color: #60a5fa;">{{ $totalLocations }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'servicos' && 'vd-tab-active'" @click="activeTab = 'servicos'">
                Serviços <span class="vd-tab-badge" style="background: rgba(234,88,12,0.15); color: #fb923c;">{{ $totalServiceOrders }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'multas' && 'vd-tab-active'" @click="activeTab = 'multas'">
                Multas <span class="vd-tab-badge" style="background: rgba(239,68,68,0.15); color: #f87171;">{{ $totalFines }}</span>
            </button>
            <button class="vd-tab" :class="activeTab === 'financeiro' && 'vd-tab-active'" @click="activeTab = 'financeiro'">
                Financeiro
            </button>
            @if($vehicle->photos->count() > 0)
                <button class="vd-tab" :class="activeTab === 'fotos' && 'vd-tab-active'" @click="activeTab = 'fotos'">
                    Fotos <span class="vd-tab-badge" style="background: rgba(251,146,60,0.15); color: #fb923c;">{{ $vehicle->photos->count() }}</span>
                </button>
            @endif
        </div>

        {{-- ========== ABA: RESUMO ========== --}}
        <div x-show="activeTab === 'resumo'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- KPIs — fi-wi-stats-overview-stat Filament nativo --}}
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
                            <span class="fi-wi-stats-overview-stat-label">Lucro Líquido</span>
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
                            <span class="fi-wi-stats-overview-stat-label">Diária Média</span>
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

            {{-- CONTADORES — mesmo estilo fi-wi-stats-overview-stat para consistência --}}
            <div class="vd-grid vd-g3">
                @php
                    $counters = [
                        ['value' => $totalLocations, 'label' => 'Locações', 'color' => '#60a5fa'],
                        ['value' => $totalContracts, 'label' => 'Contratos', 'color' => '#38bdf8'],
                        ['value' => $totalReservations, 'label' => 'Reservas', 'color' => '#fb923c'],
                        ['value' => $totalServiceOrders, 'label' => 'Ordens de Serviço', 'color' => '#fb923c'],
                        ['value' => $totalFines, 'label' => 'Multas', 'color' => '#f87171'],
                        ['value' => $totalInspections, 'label' => 'Vistorias', 'color' => '#c084fc'],
                    ];
                @endphp
                @foreach($counters as $c)
                    <div class="fi-wi-stats-overview-stat" style="text-align: center;">
                        <div class="fi-wi-stats-overview-stat-content" style="align-items: center;">
                            <div class="fi-wi-stats-overview-stat-value" style="color: {{ $c['color'] }}; font-size: 1.5rem;">
                                {{ $c['value'] }}
                            </div>
                            <div class="fi-wi-stats-overview-stat-label-ctn" style="justify-content: center;">
                                <span class="fi-wi-stats-overview-stat-label">{{ $c['label'] }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- CONTRATO ATIVO + MANUTENCAO — fi-section nativo --}}
            <div class="vd-grid vd-g2">
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Contrato Ativo</h3>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            @if($activeContract)
                                <div class="vd-section-info">
                                    <div><span class="vd-lbl">Cliente:</span> <span class="vd-val">{{ $activeContract->customer?->name ?? '-' }}</span></div>
                                    <div><span class="vd-lbl">Período:</span> <span class="vd-val">{{ $activeContract->pickup_date?->format('d/m/Y') ?? '-' }} a {{ $activeContract->return_date?->format('d/m/Y') ?? '-' }}</span></div>
                                    <div><span class="vd-lbl">Valor:</span> <span class="vd-val-green">R$ {{ number_format((float)($activeContract->total ?? 0), 2, ',', '.') }}</span></div>
                                </div>
                            @else
                                <p class="vd-empty" style="padding: 1rem;">Nenhum contrato ativo no momento.</p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Manutenção</h3>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            @if($nextMaintenance)
                                <div class="vd-section-info">
                                    <div><span class="vd-lbl">Tipo:</span> <span class="vd-val">{{ $nextMaintenance->type ?? '-' }}</span></div>
                                    <div><span class="vd-lbl">Data:</span> <span class="vd-val">{{ $nextMaintenance->due_date?->format('d/m/Y') ?? '-' }}</span></div>
                                    <div><span class="vd-lbl">Descrição:</span> <span class="vd-val">{{ $nextMaintenance->description ?? '-' }}</span></div>
                                </div>
                            @else
                                <p class="vd-empty" style="padding: 1rem;">Nenhuma manutenção pendente.</p>
                            @endif
                        </div>
                    </div>
                </section>
            </div>
        </div>

        {{-- ========== ABA: LOCACOES ========== --}}
        <div x-show="activeTab === 'locacoes'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            <section class="fi-section fi-section-has-header">
                <header class="fi-section-header">
                    <div class="fi-section-header-text-ctn">
                        <h3 class="fi-section-header-heading">Histórico de Locações (Contratos)</h3>
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
                    <header class="fi-section-header">
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
                                            <td style="text-align: right; font-weight: 600; color: #fb923c;">R$ {{ number_format((float)($res->total ?? 0), 2, ',', '.') }}</td>
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
                <header class="fi-section-header">
                    <div class="fi-section-header-text-ctn">
                        <h3 class="fi-section-header-heading">Ordens de Serviço</h3>
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
                                    <th>Descrição</th>
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
                    <header class="fi-section-header">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading">Multas de Trânsito</h3>
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
                                        <th>Descrição</th>
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
                <header class="fi-section-header">
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
                                        <th>Observação</th>
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

        {{-- ========== ABA: MULTAS ========== --}}
        <div x-show="activeTab === 'multas'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            <section class="fi-section fi-section-has-header">
                <header class="fi-section-header">
                    <div class="fi-section-header-text-ctn">
                        <h3 class="fi-section-header-heading">Multas de Trânsito</h3>
                        <p class="fi-section-header-description" style="font-size: 0.8125rem; color: #94a3b8; margin-top: 0.15rem;">
                            Histórico de autuações vinculadas a este veículo — total: <strong style="color: #f87171;">R$ {{ number_format($expensesFines, 2, ',', '.') }}</strong>
                        </p>
                    </div>
                    <div class="fi-section-header-after-ctn">
                        <a href="{{ url('/admin/fine-traffic/create?vehicle_id='.$vehicle->id) }}"
                           style="display:inline-flex;align-items:center;gap:0.35rem;padding:0.45rem 0.95rem;
                                  background:linear-gradient(135deg, #2563eb, #1d4ed8);color:#fff;
                                  border-radius:0.4rem;font-size:0.8125rem;font-weight:600;text-decoration:none;
                                  box-shadow:0 2px 6px rgba(37,99,235,0.35);transition:opacity 0.15s;"
                           onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                            + Lançar Multa
                        </a>
                    </div>
                </header>
                <div class="fi-section-content-ctn">
                    <div style="overflow-x: auto;">
                        <table class="vd-table">
                            <thead>
                                <tr>
                                    <th>AIT</th>
                                    <th>Data</th>
                                    <th>Descrição</th>
                                    <th>Condutor / Cliente</th>
                                    <th>Vencimento</th>
                                    <th style="text-align: right;">Valor</th>
                                    <th style="text-align: center;">Responsável</th>
                                    <th style="text-align: center;">Status</th>
                                    <th style="text-align: center;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vehicle->fines->sortByDesc('fine_date') as $fine)
                                    @php
                                        $fStatus = $fine->status ?? 'pendente';
                                        $fColor = match($fStatus) {
                                            'pago', 'paga' => 'background: rgba(34,197,94,0.15); color: #4ade80;',
                                            'pendente' => 'background: rgba(234,179,8,0.15); color: #facc15;',
                                            'indicado', 'transferida' => 'background: rgba(59,130,246,0.15); color: #60a5fa;',
                                            'recorrido', 'recurso' => 'background: rgba(148,163,184,0.15); color: #cbd5e1;',
                                            'cancelado', 'cancelada' => 'background: rgba(239,68,68,0.15); color: #f87171;',
                                            default => 'background: rgba(107,114,128,0.15); color: #9ca3af;',
                                        };
                                        $driverLabel = $fine->driver_name ?: ($fine->customer?->name ?? '—');
                                    @endphp
                                    <tr>
                                        <td style="font-weight: 600;">{{ $fine->auto_infraction_number ?: '—' }}</td>
                                        <td style="color: #cbd5e1;">{{ $fine->fine_date?->format('d/m/Y') ?? '—' }}</td>
                                        <td style="max-width: 280px; white-space: normal;">{{ \Illuminate\Support\Str::limit($fine->description, 70) }}</td>
                                        <td>{{ $driverLabel }}</td>
                                        <td style="color: {{ $fine->isOverdue() ? '#f87171' : '#cbd5e1' }};">
                                            {{ $fine->due_date?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: #f87171;">
                                            R$ {{ number_format((float)$fine->amount, 2, ',', '.') }}
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="vd-badge" style="background: rgba(148,163,184,0.12); color: #cbd5e1;">
                                                {{ $fine->responsibility === 'locadora' ? 'Locadora' : 'Cliente' }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="vd-badge" style="{{ $fColor }}">{{ ucfirst($fStatus) }}</span>
                                        </td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <a href="{{ url('/admin/fine-traffic/'.$fine->id.'/edit') }}"
                                               style="color:#60a5fa;text-decoration:none;font-size:0.75rem;font-weight:600;margin-right:0.5rem;">
                                                Editar
                                            </a>
                                            @if($fine->driver_name && $fine->driver_cpf)
                                                <a href="{{ route('admin.fines-traffic.fici', ['id' => $fine->id]) }}" target="_blank"
                                                   style="color:#4ade80;text-decoration:none;font-size:0.75rem;font-weight:600;">
                                                    FICI
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="vd-empty">Nenhuma multa registrada para este veículo.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        {{-- ========== ABA: FINANCEIRO ========== --}}
        <div x-show="activeTab === 'financeiro'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- RECEITAS + DESPESAS --}}
            <div class="vd-grid vd-g2">
                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading" style="color: #4ade80;">Receitas</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 1rem; font-weight: 800; color: #4ade80;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            <div class="vd-section-info">
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="vd-lbl">Contratos ({{ $totalContracts }})</span>
                                    <span class="vd-val">R$ {{ number_format($revenueContracts, 2, ',', '.') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="vd-lbl">Reservas ({{ $totalReservations }})</span>
                                    <span class="vd-val">R$ {{ number_format($revenueReservations, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="fi-section fi-section-has-header">
                    <header class="fi-section-header">
                        <div class="fi-section-header-text-ctn">
                            <h3 class="fi-section-header-heading" style="color: #f87171;">Despesas</h3>
                        </div>
                        <div class="fi-section-header-after-ctn">
                            <span style="font-size: 1rem; font-weight: 800; color: #f87171;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</span>
                        </div>
                    </header>
                    <div class="fi-section-content-ctn">
                        <div class="fi-section-content">
                            <div class="vd-section-info">
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="vd-lbl">Ordens de Serviço ({{ $totalServiceOrders }})</span>
                                    <span class="vd-val">R$ {{ number_format($expensesOS, 2, ',', '.') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="vd-lbl">Multas ({{ $totalFines }})</span>
                                    <span class="vd-val">R$ {{ number_format($expensesFines, 2, ',', '.') }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span class="vd-lbl">Seguro</span>
                                    <span class="vd-val">R$ {{ number_format($expensesInsurance, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- RESULTADO --}}
            <div class="fi-wi-stats-overview-stat" style="{{ $profit >= 0 ? 'outline-color: rgba(34,197,94,0.25);' : 'outline-color: rgba(239,68,68,0.25);' }}">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div class="fi-wi-stats-overview-stat-label">Resultado Líquido</div>
                        <div class="fi-wi-stats-overview-stat-description" style="margin-top: 0.25rem;">
                            <span>Receita - Despesas</span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.75rem; font-weight: 800; color: {{ $profit >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                        <div style="font-size: 0.8125rem; color: {{ $roi >= 0 ? '#4ade80' : '#f87171' }};">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
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
                        ['label' => 'Diária Configurada', 'value' => $vehicle->daily_rate, 'color' => '#4ade80'],
                    ];
                @endphp
                @foreach($values as $item)
                    <div class="fi-wi-stats-overview-stat" style="text-align: center;">
                        <div class="fi-wi-stats-overview-stat-content" style="align-items: center;">
                            <div class="fi-wi-stats-overview-stat-label-ctn" style="justify-content: center;">
                                <span class="fi-wi-stats-overview-stat-label">{{ $item['label'] }}</span>
                            </div>
                            <div class="fi-wi-stats-overview-stat-value" style="color: {{ $item['color'] }}; font-size: 1.125rem;">
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
                    <header class="fi-section-header">
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