<x-filament-panels::page>
<style>
    .rpt-grid { display: grid; gap: 1rem; }
    .rpt-grid-6 { grid-template-columns: repeat(6, 1fr); }
    .rpt-grid-3 { grid-template-columns: repeat(3, 1fr); }
    .rpt-grid-2 { grid-template-columns: 1fr 1fr; }
    .rpt-card { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1.25rem; }
    .rpt-card-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.5rem; }
    .rpt-card-value { font-size: 1.75rem; font-weight: 800; }
    .rpt-card-sub { font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem; }
    .rpt-section { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; overflow: hidden; }
    .rpt-section-header { padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center; }
    .rpt-section-header h3 { font-weight: 700; font-size: 0.95rem; color: #e5e7eb; margin: 0; }
    .rpt-table { width: 100%; font-size: 0.875rem; border-collapse: collapse; }
    .rpt-table thead tr { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; }
    .rpt-table th, .rpt-table td { padding: 0.625rem 1.25rem; text-align: left; }
    .rpt-table tbody tr { border-bottom: 1px solid rgba(255,255,255,0.04); }
    .rpt-table tbody tr:hover { background: rgba(255,255,255,0.02); }
    .rpt-badge { padding: 0.2rem 0.5rem; border-radius: 0.375rem; font-size: 0.7rem; font-weight: 600; }
    .rpt-filter-section { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1rem; }
    .rpt-filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.75rem; align-items: end; }
    .rpt-filter-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; color: #9ca3af; margin-bottom: 0.35rem; display: block; }
    .rpt-filter-input, .rpt-filter-select { width: 100%; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 0.5rem; color: #e5e7eb; font-size: 0.85rem; outline: none; transition: border-color 0.2s; }
    .rpt-filter-input:focus, .rpt-filter-select:focus { border-color: #f59e0b; }
    .rpt-filter-select option { background: #1f2937; color: #e5e7eb; }
    .rpt-btn { padding: 0.5rem 1rem; border: none; border-radius: 0.5rem; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: opacity 0.2s; display: inline-flex; align-items: center; gap: 0.4rem; }
    .rpt-btn:hover { opacity: 0.85; }
    .rpt-btn-primary { background: #f59e0b; color: #000; }
    .rpt-btn-secondary { background: rgba(255,255,255,0.06); color: #9ca3af; }
    .rpt-btn-pdf { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.2); }
    .rpt-btn-excel { background: rgba(34,197,94,0.15); color: #4ade80; border: 1px solid rgba(34,197,94,0.2); }
    .rpt-empty { padding: 2rem; text-align: center; color: #6b7280; font-size: 0.875rem; }
    .rpt-text-right { text-align: right; }
    .rpt-chart-container { padding: 1.25rem; }
    .rpt-error { background: rgba(239,68,68,0.08); border: 1px solid rgba(239,68,68,0.2); color: #f87171; padding: 0.75rem 1rem; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.85rem; }
    @media (max-width: 1024px) { .rpt-grid-6 { grid-template-columns: repeat(3, 1fr); } .rpt-grid-2 { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .rpt-grid-6 { grid-template-columns: repeat(2, 1fr); } .rpt-grid-3 { grid-template-columns: 1fr; } }
</style>

@if(isset($error))
    <div class="rpt-error">{{ $error }}</div>
@endif

{{-- Filtros --}}
<div class="rpt-filter-section">
    <form action="{{ request()->url() }}" method="GET">
        <div class="rpt-filter-grid">
            <div>
                <label class="rpt-filter-label">📅 Data Inicial</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rpt-filter-input">
            </div>
            <div>
                <label class="rpt-filter-label">📅 Data Final</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rpt-filter-input">
            </div>
            <div>
                <label class="rpt-filter-label">Status</label>
                <select name="status" class="rpt-filter-select">
                    <option value="">Todos</option>
                    @foreach(['aberta' => 'Aberta', 'vencida' => 'Vencida', 'paga' => 'Paga', 'cancelada' => 'Cancelada'] as $val => $lbl)
                        <option value="{{ $val }}" {{ ($filters['status'] ?? '') == $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="rpt-filter-label">Cliente</label>
                <select name="customer_id" class="rpt-filter-select">
                    <option value="">Todos</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ ($filters['customer_id'] ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
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
                <button type="submit" class="rpt-btn rpt-btn-primary">🔍 Filtrar</button>
                <a href="{{ request()->url() }}" class="rpt-btn rpt-btn-secondary">Limpar</a>
            </div>
        </div>
    </form>
</div>

{{-- KPI Cards --}}
<div class="rpt-grid rpt-grid-6" style="margin-bottom:1rem;">
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">📋 Total</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $totalCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($totalAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">📂 Abertas</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $openCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($openAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(239,68,68,0.06); border-color:rgba(239,68,68,0.2);">
        <div class="rpt-card-label" style="color:#f87171;">⚠️ Vencidas</div>
        <div class="rpt-card-value" style="color:#f87171;">{{ $overdueCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($overdueAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(34,197,94,0.06); border-color:rgba(34,197,94,0.2);">
        <div class="rpt-card-label" style="color:#4ade80;">✅ Pagas</div>
        <div class="rpt-card-value" style="color:#4ade80;">{{ $paidCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($paidAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(107,114,128,0.06); border-color:rgba(107,114,128,0.2);">
        <div class="rpt-card-label" style="color:#9ca3af;">🚫 Canceladas</div>
        <div class="rpt-card-value" style="color:#9ca3af;">{{ $cancelledCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($cancelledAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(245,158,11,0.06); border-color:rgba(245,158,11,0.2);">
        <div class="rpt-card-label" style="color:#fbbf24;">📊 Recebimento</div>
        <div class="rpt-card-value" style="color:#fbbf24;">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%</div>
        <div class="rpt-card-sub">Pagas vs Total</div>
    </div>
</div>

{{-- Gráficos --}}
<div class="rpt-grid rpt-grid-2" style="margin-bottom:1rem;">
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>📊 Distribuição por Status</h3></div>
        <div class="rpt-chart-container"><canvas id="statusChart" style="max-height:280px;"></canvas></div>
    </div>
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>📈 Tendência Mensal</h3></div>
        <div class="rpt-chart-container"><canvas id="monthlyChart" style="max-height:280px;"></canvas></div>
    </div>
</div>

{{-- Botões de Exportação + Tabela de Dados --}}
<div class="rpt-section">
    <div class="rpt-section-header">
        <h3>📋 Faturas Detalhadas</h3>
        <div style="display:flex; gap:0.5rem;">
            <button onclick="exportPdf()" class="rpt-btn rpt-btn-pdf">📥 PDF</button>
            <button onclick="exportExcel()" class="rpt-btn rpt-btn-excel">📊 Excel</button>
        </div>
    </div>
    <table class="rpt-table">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th>Contrato</th>
                <th>Vencimento</th>
                <th>Status</th>
                <th class="rpt-text-right">Valor</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
                @php
                    $sc = [
                        'aberta'    => ['bg' => 'rgba(59,130,246,0.12)',  'color' => '#60a5fa'],
                        'vencida'   => ['bg' => 'rgba(239,68,68,0.12)',  'color' => '#f87171'],
                        'paga'      => ['bg' => 'rgba(34,197,94,0.12)',  'color' => '#4ade80'],
                        'cancelada' => ['bg' => 'rgba(107,114,128,0.12)','color' => '#9ca3af'],
                    ];
                    $statusVal = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;
                    $s = $sc[$statusVal] ?? $sc['cancelada'];
                @endphp
                <tr>
                    <td style="color:#e5e7eb; font-weight:600;">{{ $invoice->invoice_number }}</td>
                    <td style="color:#e5e7eb;">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td style="color:#9ca3af;">{{ $invoice->contract->number ?? 'N/A' }}</td>
                    <td style="color:#e5e7eb;">{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td><span class="rpt-badge" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">{{ ucfirst($statusVal) }}</span></td>
                    <td class="rpt-text-right" style="color:#e5e7eb; font-weight:600;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="rpt-empty">Nenhuma fatura encontrada no período selecionado</td></tr>
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
            datasets: [{ data: {!! json_encode($statusData['data']) !!}, backgroundColor: ['rgba(59,130,246,0.7)','rgba(239,68,68,0.7)','rgba(34,197,94,0.7)','rgba(107,114,128,0.7)'], borderColor: 'rgba(255,255,255,0.05)', borderWidth: 1 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: darkOpts } } }
    });

    const md = {!! json_encode($monthlyData) !!};
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: md.map(d => d.month),
            datasets: [{ label: 'Faturas', data: md.map(d => d.count), borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.08)', borderWidth: 2, tension: 0.4, fill: true, pointBackgroundColor: '#f59e0b' }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: darkOpts } }, scales: { y: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, x: { ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });

    function exportPdf() { window.location.href = '/export/invoices/pdf?' + new URLSearchParams(window.location.search).toString(); }
    function exportExcel() { window.location.href = '/export/invoices/excel?' + new URLSearchParams(window.location.search).toString(); }
</script>
</x-filament-panels::page>
