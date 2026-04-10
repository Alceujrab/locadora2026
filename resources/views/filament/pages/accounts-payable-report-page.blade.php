<x-filament-panels::page>
{{-- CSS classes loaded via custom-theme.blade.php --}}

@if(isset($error))
    <div class="rpt-error">{{ $error }}</div>
@endif

{{-- Filtros --}}
<div class="rpt-filter-section">
    <form action="{{ request()->url() }}" method="GET">
        <div class="rpt-filter-grid">
            <div>
                <label class="rpt-filter-label">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rpt-filter-input">
            </div>
            <div>
                <label class="rpt-filter-label">Data Final</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rpt-filter-input">
            </div>
            <div>
                <label class="rpt-filter-label">Status</label>
                <select name="status" class="rpt-filter-select">
                    <option value="">Todos</option>
                    @foreach(['pendente' => 'Pendente', 'pago' => 'Pago', 'cancelado' => 'Cancelado'] as $val => $lbl)
                        <option value="{{ $val }}" {{ ($filters['status'] ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rpt-filter-label">Categoria</label>
                <select name="category" class="rpt-filter-select">
                    <option value="">Todas</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ ($filters['category'] ?? '') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rpt-filter-label">Fornecedor</label>
                <select name="supplier_id" class="rpt-filter-select">
                    <option value="">Todos</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->id }}" {{ ($filters['supplier_id'] ?? '') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rpt-filter-label">Filial</label>
                <select name="branch_id" class="rpt-filter-select">
                    <option value="">Todas</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ ($filters['branch_id'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex; gap:0.5rem; align-items:flex-end;">
                <button type="submit" class="rpt-btn rpt-btn-primary">Filtrar</button>
                <a href="{{ request()->url() }}" class="rpt-btn rpt-btn-secondary">Limpar</a>
            </div>
        </div>
    </form>
</div>

{{-- KPI Cards --}}
<div class="rpt-grid rpt-grid-6" style="margin-bottom:1rem;">
    <div class="rpt-card" style="background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2);">
        <div class="rpt-card-label" style="color:#f87171;">Total a Pagar</div>
        <div class="rpt-card-value" style="color:#f87171;">{{ $totalCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($totalAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(249,115,22,0.06); border-color:rgba(249,115,22,0.2);">
        <div class="rpt-card-label" style="color:#fbbf24;">Pendentes</div>
        <div class="rpt-card-value" style="color:#fbbf24;">{{ $pendingCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($pendingAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2);">
        <div class="rpt-card-label" style="color:#f87171;">Vencidas</div>
        <div class="rpt-card-value" style="color:#f87171;">{{ $overdueCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($overdueAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(34,197,94,0.06); border-color:rgba(34,197,94,0.2);">
        <div class="rpt-card-label" style="color:#4ade80;">Pagas</div>
        <div class="rpt-card-value" style="color:#4ade80;">{{ $paidCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($paidAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(107,114,128,0.06); border-color:rgba(107,114,128,0.2);">
        <div class="rpt-card-label" style="color:#9ca3af;">Canceladas</div>
        <div class="rpt-card-value" style="color:#9ca3af;">{{ $cancelledCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($cancelledAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(249,115,22,0.06); border-color:rgba(249,115,22,0.2);">
        <div class="rpt-card-label" style="color:#fb923c;">Pagamento</div>
        <div class="rpt-card-value" style="color:#fb923c;">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%</div>
        <div class="rpt-card-sub">Pagas vs Total</div>
    </div>
</div>

{{-- Gráficos --}}
<div class="rpt-grid rpt-grid-2" style="margin-bottom:1rem;">
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Distribuição por Status</h3></div>
        <div class="rpt-chart-container"><canvas id="statusChart" style="max-height:280px;"></canvas></div>
    </div>
    @if(count($categoryData['labels']) > 0)
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Despesas por Categoria</h3></div>
        <div class="rpt-chart-container"><canvas id="categoryChart" style="max-height:280px;"></canvas></div>
    </div>
    @endif
</div>

{{-- Tabela de Dados --}}
<div class="rpt-section">
    <div class="rpt-section-header">
        <h3>Contas a Pagar Detalhadas</h3>
        <div style="display:flex; gap:0.5rem;">
            <button onclick="exportPdf()" class="rpt-btn rpt-btn-pdf">PDF</button>
            <button onclick="exportExcel()" class="rpt-btn rpt-btn-excel">Excel</button>
        </div>
    </div>
    <table class="rpt-table">
        <thead>
            <tr>
                <th>Descrição</th>
                <th>Fornecedor</th>
                <th>Vencimento</th>
                <th>Categoria</th>
                <th class="rpt-text-right">Valor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
                @php
                    $sc = [
                        'pendente'  => ['bg' => 'rgba(249,115,22,0.12)',  'color' => '#fb923c'],
                        'pago'      => ['bg' => 'rgba(34,197,94,0.12)',   'color' => '#4ade80'],
                        'cancelado' => ['bg' => 'rgba(107,114,128,0.12)', 'color' => '#9ca3af'],
                    ];
                    $s = $sc[$record->status] ?? $sc['cancelado'];
                @endphp
                <tr>
                    <td style="color:#e5e7eb; font-weight:600;">{{ $record->description }}</td>
                    <td style="color:#e5e7eb;">{{ $record->supplier->name ?? 'N/A' }}</td>
                    <td style="color:#e5e7eb;">{{ $record->due_date->format('d/m/Y') }}</td>
                    <td style="color:#9ca3af;">{{ ucfirst($record->category) }}</td>
                    <td class="rpt-text-right" style="color:#e5e7eb; font-weight:600;">R$ {{ number_format($record->amount, 2, ',', '.') }}</td>
                    <td><span class="rpt-badge" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">{{ ucfirst($record->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="rpt-empty">Nenhuma conta a pagar encontrada no período selecionado</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const darkOpts = { color: '#9ca3af', font: { size: 11 } };

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($statusData['labels']) !!},
            datasets: [{ data: {!! json_encode($statusData['data']) !!}, backgroundColor: ['rgba(249,115,22,0.7)','rgba(239,68,68,0.7)','rgba(34,197,94,0.7)','rgba(107,114,128,0.7)'], borderColor: 'rgba(255,255,255,0.05)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: darkOpts } } }
    });

    @if(count($categoryData['labels']) > 0)
    const catColors = ['rgba(59,130,246,0.6)','rgba(249,115,22,0.6)','rgba(34,197,94,0.6)','rgba(239,68,68,0.6)','rgba(14,165,233,0.6)','rgba(34,211,238,0.6)','rgba(244,114,182,0.6)','rgba(163,230,53,0.6)'];
    new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($categoryData['labels']) !!},
            datasets: [{ label: 'Valor (R$)', data: {!! json_encode($categoryData['data']) !!}, backgroundColor: catColors.slice(0, {!! count($categoryData['labels']) !!}), borderColor: 'rgba(255,255,255,0.05)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, x: { ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });
    @endif

    function exportPdf() { window.location.href = '/export/accounts-payable/pdf?' + new URLSearchParams(window.location.search).toString(); }
    function exportExcel() { window.location.href = '/export/accounts-payable/excel?' + new URLSearchParams(window.location.search).toString(); }
</script>
</x-filament-panels::page>
