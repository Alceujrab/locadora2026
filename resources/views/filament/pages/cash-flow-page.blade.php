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
                <label class="rpt-filter-label">Tipo</label>
                <select name="type" class="rpt-filter-select">
                    <option value="">Todos</option>
                    <option value="entrada" {{ ($filters['type'] ?? '') == 'entrada' ? 'selected' : '' }}>Entradas</option>
                    <option value="saida" {{ ($filters['type'] ?? '') == 'saida' ? 'selected' : '' }}>Saidas</option>
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
        <div class="rpt-card-label" style="color:#4ade80;">Entradas</div>
        <div class="rpt-card-value" style="color:#4ade80;">R$ {{ number_format($totalIn, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Recebiveis pagos</div>
    </div>
    <div class="rpt-card" style="background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2);">
        <div class="rpt-card-label" style="color:#f87171;">Saidas</div>
        <div class="rpt-card-value" style="color:#f87171;">R$ {{ number_format($totalOut, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Contas pagas</div>
    </div>
    <div class="rpt-card" style="background:rgba({{ $netFlow >= 0 ? '34,197,94' : '239,68,68' }},0.06); border-color:rgba({{ $netFlow >= 0 ? '34,197,94' : '239,68,68' }},0.2);">
        <div class="rpt-card-label" style="color:{{ $netFlow >= 0 ? '#4ade80' : '#f87171' }};">Saldo</div>
        <div class="rpt-card-value" style="color:{{ $netFlow >= 0 ? '#4ade80' : '#f87171' }};">R$ {{ number_format($netFlow, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Entradas - Saidas</div>
    </div>
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">Transacoes</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $transactionCount }}</div>
        <div class="rpt-card-sub">No periodo</div>
    </div>
    <div class="rpt-card" style="background:rgba(139,92,246,0.06); border-color:rgba(139,92,246,0.2);">
        <div class="rpt-card-label" style="color:#fb923c;">A Receber</div>
        <div class="rpt-card-value" style="color:#fb923c;">R$ {{ number_format($projectedIn, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Pendente periodo</div>
    </div>
    <div class="rpt-card" style="background:rgba(249,115,22,0.06); border-color:rgba(249,115,22,0.2);">
        <div class="rpt-card-label" style="color:#fbbf24;">Projecao</div>
        <div class="rpt-card-value" style="color:#fbbf24;">R$ {{ number_format($projectedBalance, 2, ',', '.') }}</div>
        <div class="rpt-card-sub">Saldo + Previsto</div>
    </div>
</div>

{{-- Graficos --}}
<div class="rpt-grid rpt-grid-2" style="margin-bottom:1rem;">
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Entradas vs Saidas</h3></div>
        <div class="rpt-chart-container"><canvas id="distributionChart" style="max-height:280px;"></canvas></div>
    </div>
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Fluxo Diario</h3></div>
        <div class="rpt-chart-container"><canvas id="dailyChart" style="max-height:280px;"></canvas></div>
    </div>
</div>

{{-- Tabela de Transacoes --}}
<div class="rpt-section">
    <div class="rpt-section-header">
        <h3>Movimentacoes</h3>
        <div style="display:flex; gap:0.5rem;">
            <button onclick="exportPdf()" class="rpt-btn rpt-btn-pdf">PDF</button>
            <button onclick="exportExcel()" class="rpt-btn rpt-btn-excel">Excel</button>
        </div>
    </div>
    <table class="rpt-table">
        <thead>
            <tr>
                <th>Data</th>
                <th>Tipo</th>
                <th>Descricao</th>
                <th>Entidade</th>
                <th class="rpt-text-right">Valor</th>
                <th class="rpt-text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                @php
                    $isIn = $t['type'] === 'entrada';
                    $bgColor = $isIn ? 'rgba(34,197,94,0.12)' : 'rgba(239,68,68,0.12)';
                    $textColor = $isIn ? '#4ade80' : '#f87171';
                @endphp
                <tr>
                    <td style="color:#e5e7eb;">{{ $t['date']->format('d/m/Y') }}</td>
                    <td><span class="rpt-badge" style="background:{{ $bgColor }}; color:{{ $textColor }};">{{ $isIn ? 'Entrada' : 'Saida' }}</span></td>
                    <td style="color:#e5e7eb;">{{ \Illuminate\Support\Str::limit($t['description'], 50) }}</td>
                    <td style="color:#9ca3af;">{{ $t['entity'] }}</td>
                    <td class="rpt-text-right" style="color:{{ $textColor }}; font-weight:600;">
                        {{ $isIn ? '+' : '-' }} R$ {{ number_format($t['amount'], 2, ',', '.') }}
                    </td>
                    <td class="rpt-text-right" style="color:{{ $t['balance'] >= 0 ? '#4ade80' : '#f87171' }}; font-weight:600;">
                        R$ {{ number_format($t['balance'], 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="rpt-empty">Nenhuma movimentacao encontrada no periodo selecionado</td></tr>
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

    // Daily flow chart
    const dd = {!! json_encode($dailyData) !!};
    new Chart(document.getElementById('dailyChart'), {
        type: 'bar',
        data: {
            labels: dd.map(d => d.day),
            datasets: [
                { label: 'Entradas', data: dd.map(d => d.in), backgroundColor: 'rgba(34,197,94,0.6)', borderRadius: 4 },
                { label: 'Saidas', data: dd.map(d => d.out), backgroundColor: 'rgba(239,68,68,0.6)', borderRadius: 4 },
            ]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: darkOpts } }, scales: { y: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, x: { ticks: { ...darkOpts, maxTicksLimit: 15 }, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });

    function exportPdf() { window.location.href = '/export/cashflow/pdf?' + new URLSearchParams(window.location.search).toString(); }
    function exportExcel() { window.location.href = '/export/cashflow/excel?' + new URLSearchParams(window.location.search).toString(); }
</script>
</x-filament-panels::page>
