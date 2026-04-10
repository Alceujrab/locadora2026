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
                <label class="rpt-filter-label">Filial</label>
                <select name="branch_id" class="rpt-filter-select">
                    <option value="">Todas</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ ($filters['branch_id'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rpt-filter-label">Veiculo</label>
                <select name="vehicle_id" class="rpt-filter-select">
                    <option value="">Todos</option>
                    @foreach($allVehicles as $v)
                        <option value="{{ $v->id }}" {{ ($filters['vehicle_id'] ?? '') == $v->id ? 'selected' : '' }}>{{ $v->plate }} - {{ $v->brand }} {{ $v->model }}</option>
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
    <div class="rpt-card" style="background:rgba(34,197,94,0.06); border-color:rgba(34,197,94,0.2);">
        <div class="rpt-card-label" style="color:#4ade80;">Receita Total</div>
        <div class="rpt-card-value" style="color:#4ade80;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Contratos ativos/finalizados</div>
    </div>
    <div class="rpt-card" style="background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2);">
        <div class="rpt-card-label" style="color:#f87171;">Despesas Total</div>
        <div class="rpt-card-value" style="color:#f87171;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Contas a pagar (veiculos)</div>
    </div>
    <div class="rpt-card" style="background:rgba({{ $totalProfit >= 0 ? '34,197,94' : '239,68,68' }},0.06); border-color:rgba({{ $totalProfit >= 0 ? '34,197,94' : '239,68,68' }},0.2);">
        <div class="rpt-card-label" style="color:{{ $totalProfit >= 0 ? '#4ade80' : '#f87171' }};">Lucro Liquido</div>
        <div class="rpt-card-value" style="color:{{ $totalProfit >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($totalProfit, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Receita - Despesas</div>
    </div>
    <div class="rpt-card" style="background:rgba(249,115,22,0.06); border-color:rgba(249,115,22,0.2);">
        <div class="rpt-card-label" style="color:#fbbf24;">Margem</div>
        <div class="rpt-card-value" style="color:#fbbf24;">{{ number_format($totalMargin, 1) }}%</div>
        <div class="rpt-card-sub">Lucro / Receita</div>
    </div>
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">Veiculos</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $activeVehicles }}</div>
        <div class="rpt-card-sub">Na frota</div>
    </div>
    <div class="rpt-card" style="background:rgba(139,92,246,0.06); border-color:rgba(139,92,246,0.2);">
        <div class="rpt-card-label" style="color:#fb923c;">Receita/Veiculo</div>
        <div class="rpt-card-value" style="color:#fb923c;">R$ {{ number_format($revenuePerVehicle, 0, ',', '.') }}</div>
        <div class="rpt-card-sub">Media por veiculo</div>
    </div>
</div>

{{-- Graficos --}}
<div class="rpt-grid rpt-grid-2" style="margin-bottom:1rem;">
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Receitas vs Despesas</h3></div>
        <div class="rpt-chart-container"><canvas id="distributionChart" style="max-height:280px;"></canvas></div>
    </div>
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Tendencia Mensal</h3></div>
        <div class="rpt-chart-container"><canvas id="monthlyChart" style="max-height:280px;"></canvas></div>
    </div>
</div>

{{-- Top 10 Chart --}}
<div class="rpt-section" style="margin-bottom:1rem;">
    <div class="rpt-section-header"><h3>Top 10 Veiculos por Lucro</h3></div>
    <div class="rpt-chart-container"><canvas id="profitChart" style="max-height:300px;"></canvas></div>
</div>

{{-- Tabela Detalhada --}}
<div class="rpt-section">
    <div class="rpt-section-header">
        <h3>Lucratividade por Veiculo</h3>
        <div style="display:flex; gap:0.5rem;">
            <button onclick="exportPdf()" class="rpt-btn rpt-btn-pdf">PDF</button>
            <button onclick="exportExcel()" class="rpt-btn rpt-btn-excel">Excel</button>
        </div>
    </div>
    <table class="rpt-table">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Modelo</th>
                <th>Filial</th>
                <th>Contratos</th>
                <th class="rpt-text-right">Receita</th>
                <th class="rpt-text-right">Despesas</th>
                <th class="rpt-text-right">Lucro</th>
                <th class="rpt-text-right">Margem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicleData as $v)
                <tr>
                    <td style="color:#e5e7eb; font-weight:600;">{{ $v['plate'] }}</td>
                    <td style="color:#e5e7eb;">{{ $v['model'] }} {{ $v['year'] }}</td>
                    <td style="color:#9ca3af;">{{ $v['branch'] }}</td>
                    <td style="color:#e5e7eb;">{{ $v['contracts'] }}</td>
                    <td class="rpt-text-right" style="color:#4ade80;">R$ {{ number_format($v['revenue'], 2, ',', '.') }}</td>
                    <td class="rpt-text-right" style="color:#f87171;">R$ {{ number_format($v['expenses'], 2, ',', '.') }}</td>
                    <td class="rpt-text-right {{ $v['profit'] >= 0 ? 'rpt-profit-positive' : 'rpt-profit-negative' }}" style="font-weight:700;">
                        R$ {{ number_format($v['profit'], 2, ',', '.') }}
                    </td>
                    <td class="rpt-text-right {{ $v['margin'] >= 0 ? 'rpt-profit-positive' : 'rpt-profit-negative' }}">
                        {{ number_format($v['margin'], 1) }}%
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="rpt-empty">Nenhum veiculo encontrado no periodo selecionado</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const darkOpts = { color: '#9ca3af', font: { size: 11 } };

    // Distribution doughnut
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($distributionData['labels']) !!},
            datasets: [{ data: {!! json_encode($distributionData['data']) !!}, backgroundColor: ['rgba(34,197,94,0.7)','rgba(239,68,68,0.7)'], borderColor: 'rgba(255,255,255,0.05)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: darkOpts } } }
    });

    // Monthly trend
    const md = {!! json_encode($monthlyData) !!};
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: md.map(d => d.month),
            datasets: [
                { label: 'Receita', data: md.map(d => d.revenue), backgroundColor: 'rgba(34,197,94,0.6)', borderRadius: 4 },
                { label: 'Despesa', data: md.map(d => d.expenses), backgroundColor: 'rgba(239,68,68,0.6)', borderRadius: 4 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: darkOpts } }, scales: { y: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, x: { ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });

    // Top 10 profit chart
    const pc = {!! json_encode($profitChartData) !!};
    new Chart(document.getElementById('profitChart'), {
        type: 'bar',
        data: {
            labels: pc.labels,
            datasets: [
                { label: 'Receita', data: pc.revenue, backgroundColor: 'rgba(34,197,94,0.6)', borderRadius: 4 },
                { label: 'Despesa', data: pc.expenses, backgroundColor: 'rgba(239,68,68,0.6)', borderRadius: 4 },
                { label: 'Lucro', data: pc.profit, backgroundColor: 'rgba(249,115,22,0.6)', borderRadius: 4 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: true, indexAxis: 'y', plugins: { legend: { labels: darkOpts } }, scales: { x: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, y: { ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });

    function exportPdf() { window.location.href = '/export/fleet-profitability/pdf?' + new URLSearchParams(window.location.search).toString(); }
    function exportExcel() { window.location.href = '/export/fleet-profitability/excel?' + new URLSearchParams(window.location.search).toString(); }
</script>
</x-filament-panels::page>
