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
            <div>
                <label class="rpt-filter-label">Placa Veículo</label>
                <input type="text" name="vehicle_plate" value="{{ $filters['vehicle_plate'] ?? '' }}" class="rpt-filter-input" placeholder="Ex: ABC1D23" maxlength="7" style="text-transform:uppercase;">
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
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">Total</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $totalCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($totalAmount, 2, ',', '.') }}</div>
    </div>
    <div class="rpt-card" style="background:rgba(59,130,246,0.06); border-color:rgba(59,130,246,0.2);">
        <div class="rpt-card-label" style="color:#60a5fa;">Abertas</div>
        <div class="rpt-card-value" style="color:#60a5fa;">{{ $openCount }}</div>
        <div class="rpt-card-sub">R$ {{ number_format($openAmount, 2, ',', '.') }}</div>
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
        <div class="rpt-card-label" style="color:#fbbf24;">Recebimento</div>
        <div class="rpt-card-value" style="color:#fbbf24;">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%</div>
        <div class="rpt-card-sub">Pagas vs Total</div>
    </div>
</div>

{{-- Gráficos --}}
<div class="rpt-grid rpt-grid-2" style="margin-bottom:1rem;">
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Distribuição por Status</h3></div>
        <div class="rpt-chart-container"><canvas id="statusChart" style="max-height:280px;"></canvas></div>
    </div>
    <div class="rpt-section">
        <div class="rpt-section-header"><h3>Tendência Mensal</h3></div>
        <div class="rpt-chart-container"><canvas id="monthlyChart" style="max-height:280px;"></canvas></div>
    </div>
</div>

{{-- Tabela de Dados --}}
<div class="rpt-section">
    <div class="rpt-section-header">
        <h3>Faturas Detalhadas</h3>
        <div style="display:flex; gap:0.5rem; align-items:center; flex-wrap:wrap;">
            {{-- Toggles de colunas --}}
            <div class="rpt-toggle-bar">
                <span style="font-size:0.7rem; color:#6b7280; margin-right:0.25rem;">Colunas:</span>
                <label class="rpt-toggle active" data-col="col-vehicle"><input type="checkbox" checked onchange="toggleCol('col-vehicle', this)"> Veículo</label>
                <label class="rpt-toggle" data-col="col-period"><input type="checkbox" onchange="toggleCol('col-period', this)"> Período</label>
                <label class="rpt-toggle active" data-col="col-contract"><input type="checkbox" checked onchange="toggleCol('col-contract', this)"> Contrato</label>
            </div>
            <button onclick="exportPdf()" class="rpt-btn rpt-btn-pdf">PDF</button>
            <button onclick="exportExcel()" class="rpt-btn rpt-btn-excel">Excel</button>
        </div>
    </div>
    <div style="overflow-x:auto;">
    <table class="rpt-table">
        <thead>
            <tr>
                <th>Número</th>
                <th>Cliente</th>
                <th class="col-vehicle">Veículo</th>
                <th class="col-period col-hidden">Período Reserva</th>
                <th class="col-contract">Contrato</th>
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

                    // Placa do veículo
                    $plate = $invoice->contract?->vehicle?->plate;
                    if (!$plate && $invoice->notes && preg_match('/Veiculo:\s*([A-Z0-9]{7})/i', $invoice->notes, $m)) {
                        $plate = strtoupper($m[1]);
                    }

                    // Período da reserva
                    $period = null;
                    if ($invoice->notes && preg_match('/Periodo:\s*(.+?)(?:\n|$)/i', $invoice->notes, $mp)) {
                        $period = trim($mp[1]);
                    }
                @endphp
                <tr>
                    <td style="color:#e5e7eb; font-weight:600;">{{ $invoice->invoice_number }}</td>
                    <td style="color:#e5e7eb;">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td class="col-vehicle">
                        @if($plate)
                            <span class="rpt-badge-plate">{{ $plate }}</span>
                        @else
                            <span style="color:#4b5563;">—</span>
                        @endif
                    </td>
                    <td class="col-period col-hidden" style="color:#e5e7eb; font-size:0.8rem;">{{ $period ?? '—' }}</td>
                    <td class="col-contract" style="color:#9ca3af;">{{ $invoice->contract->contract_number ?? '—' }}</td>
                    <td style="color:#e5e7eb;">{{ $invoice->due_date->format('d/m/Y') }}</td>
                    <td><span class="rpt-badge" style="background:{{ $s['bg'] }}; color:{{ $s['color'] }};">{{ ucfirst($statusVal) }}</span></td>
                    <td class="rpt-text-right" style="color:#e5e7eb; font-weight:600;">R$ {{ number_format($invoice->total, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="rpt-empty">Nenhuma fatura encontrada no período selecionado</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
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
            datasets: [{ label: 'Faturas', data: md.map(d => d.count), borderColor: '#3b82f6', backgroundColor: 'rgba(59,130,246,0.08)', borderWidth: 2, tension: 0.4, fill: true, pointBackgroundColor: '#3b82f6' }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { labels: darkOpts } }, scales: { y: { beginAtZero: true, ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } }, x: { ticks: darkOpts, grid: { color: 'rgba(255,255,255,0.04)' } } } }
    });

    // Toggle de colunas
    function toggleCol(className, checkbox) {
        const cells = document.querySelectorAll('.' + className);
        const label = checkbox.closest('.rpt-toggle');
        if (checkbox.checked) {
            cells.forEach(c => c.classList.remove('col-hidden'));
            label.classList.add('active');
        } else {
            cells.forEach(c => c.classList.add('col-hidden'));
            label.classList.remove('active');
        }
    }

    function exportPdf() { window.location.href = '/export/invoices/pdf?' + new URLSearchParams(window.location.search).toString(); }
    function exportExcel() { window.location.href = '/export/invoices/excel?' + new URLSearchParams(window.location.search).toString(); }
</script>
</x-filament-panels::page>
