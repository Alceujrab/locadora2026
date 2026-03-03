<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório do Veículo — {{ $vehicle->plate }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1a202c; margin: 0; padding: 20px 25px; font-size: 10px; }

        /* CABEÇALHO */
        .header-table { width: 100%; border-collapse: collapse; border-bottom: 3px solid #f59e0b; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .header-logo img { max-width: 65px; max-height: 55px; }
        .header-title h1 { font-size: 16px; color: #f59e0b; margin: 0 0 2px 0; }
        .header-title p { font-size: 9px; color: #64748b; margin: 2px 0; }
        .header-company { text-align: right; font-size: 8px; color: #4b5563; line-height: 1.5; }
        .header-company strong { font-size: 10px; color: #1e293b; display: block; margin-bottom: 2px; }

        /* SEÇÃO */
        .section { margin-bottom: 14px; }
        .section-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #f59e0b; letter-spacing: 0.06em; border-bottom: 2px solid #fef3c7; padding-bottom: 3px; margin-bottom: 8px; }

        /* INFO GRID */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .info-table td { padding: 3px 6px; border: none; vertical-align: top; }
        .info-label { font-weight: bold; color: #475569; width: 25%; font-size: 9px; }
        .info-value { color: #1e293b; font-size: 9px; }

        /* KPI */
        .kpi-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .kpi-table td { padding: 8px 6px; text-align: center; border: 1px solid #e5e7eb; background: #fefce8; }
        .kpi-label { font-size: 7px; font-weight: bold; text-transform: uppercase; color: #475569; display: block; margin-bottom: 2px; }
        .kpi-value { font-size: 13px; font-weight: bold; color: #f59e0b; }
        .kpi-green { color: #059669; }
        .kpi-red { color: #dc2626; }

        /* TABELA */
        table.data { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 12px; }
        table.data thead { background: #f59e0b; color: white; }
        table.data th { border: 1px solid #ddd; padding: 5px 6px; text-align: left; font-weight: bold; font-size: 8px; text-transform: uppercase; }
        table.data td { border: 1px solid #ddd; padding: 4px 6px; }
        table.data tbody tr:nth-child(even) { background: #fefce8; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* BADGE */
        .badge { padding: 1px 5px; border-radius: 3px; font-weight: bold; font-size: 7px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-gray { background: #f3f4f6; color: #374151; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-purple { background: #ede9fe; color: #5b21b6; }

        /* RODAPÉ */
        .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 10px; text-align: center; color: #94a3b8; font-size: 8px; }
        .footer strong { color: #f59e0b; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ===== CABEÇALHO ===== --}}
    <table class="header-table">
        <tr>
            <td style="width: 70px;" class="header-logo">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </td>
            <td class="header-title" style="padding-left: 10px;">
                <h1>RELATÓRIO DO VEÍCULO</h1>
                <p>{{ $vehicle->brand }} {{ $vehicle->model }} — {{ $vehicle->plate }}</p>
                <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
            </td>
            <td class="header-company">
                <strong>{{ $company['name'] }}</strong>
                @if($company['cnpj']) CNPJ: {{ $company['cnpj'] }}<br> @endif
                @if($company['phone']) Tel.: {{ $company['phone'] }}<br> @endif
                @if($company['email']) {{ $company['email'] }}<br> @endif
                @if($company['address']) {{ $company['address'] }}<br> @endif
                @if($company['city'] || $company['state'])
                    {{ $company['city'] }}{{ ($company['city'] && $company['state']) ? ' - ' : '' }}{{ $company['state'] }}
                @endif
            </td>
        </tr>
    </table>

    {{-- ===== DADOS DO VEÍCULO ===== --}}
    <div class="section">
        <div class="section-title">🚗 Dados do Veículo</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Placa:</td>
                <td class="info-value"><strong>{{ $vehicle->plate }}</strong></td>
                <td class="info-label">Categoria:</td>
                <td class="info-value">{{ $vehicle->category->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="info-label">Marca / Modelo:</td>
                <td class="info-value">{{ $vehicle->brand }} {{ $vehicle->model }}</td>
                <td class="info-label">Ano:</td>
                <td class="info-value">{{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}</td>
            </tr>
            <tr>
                <td class="info-label">Cor:</td>
                <td class="info-value">{{ $vehicle->color ?? '—' }}</td>
                <td class="info-label">Combustível:</td>
                <td class="info-value">{{ $vehicle->fuel ?? '—' }}</td>
            </tr>
            <tr>
                <td class="info-label">Quilometragem:</td>
                <td class="info-value">{{ number_format((float)$vehicle->mileage, 0, ',', '.') }} km</td>
                <td class="info-label">Filial:</td>
                <td class="info-value">{{ $vehicle->branch->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="info-label">Chassi:</td>
                <td class="info-value">{{ $vehicle->chassis ?? '—' }}</td>
                <td class="info-label">Renavam:</td>
                <td class="info-value">{{ $vehicle->renavam ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== KPIs RESUMO ===== --}}
    <table class="kpi-table">
        <tr>
            <td>
                <span class="kpi-label">Total Locações</span>
                <span class="kpi-value">{{ $totalLocations }}</span>
            </td>
            <td>
                <span class="kpi-label">Dias Locado</span>
                <span class="kpi-value">{{ $totalDaysRented }}</span>
            </td>
            <td>
                <span class="kpi-label">Receita Total</span>
                <span class="kpi-value kpi-green">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="kpi-label">Despesas Total</span>
                <span class="kpi-value kpi-red">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="kpi-label">Lucro Líquido</span>
                <span class="kpi-value {{ $profit >= 0 ? 'kpi-green' : 'kpi-red' }}">R$ {{ number_format($profit, 2, ',', '.') }}</span>
            </td>
            <td>
                <span class="kpi-label">Diária Média</span>
                <span class="kpi-value">R$ {{ number_format($avgDailyRate, 2, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    {{-- ===== CONTRATOS ===== --}}
    @if($vehicle->contracts->count() > 0)
    <div class="section">
        <div class="section-title">📋 Histórico de Contratos ({{ $totalContracts }})</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Contrato</th>
                    <th>Cliente</th>
                    <th>Período</th>
                    <th class="text-center">Dias</th>
                    <th class="text-right">Diária</th>
                    <th class="text-right">Valor Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->contracts->sortByDesc('created_at') as $c)
                <tr>
                    <td style="font-weight:bold;">{{ $c->contract_number }}</td>
                    <td>{{ $c->customer->name ?? '—' }}</td>
                    <td>{{ $c->pickup_date?->format('d/m/Y') }} a {{ $c->return_date?->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $c->total_days }}</td>
                    <td class="text-right">R$ {{ number_format((float)($c->daily_rate ?? 0), 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:bold;">R$ {{ number_format((float)($c->total ?? 0), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php $sv = $c->status instanceof \BackedEnum ? $c->status->value : $c->status; @endphp
                        <span class="badge {{ $sv === 'ativo' ? 'badge-green' : 'badge-gray' }}">{{ ucfirst($sv) }}</span>
                    </td>
                </tr>
                @endforeach
                <tr style="background:#fef9c3; font-weight:bold;">
                    <td colspan="5">Total Contratos</td>
                    <td class="text-right">R$ {{ number_format($revenueContracts, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ===== RESERVAS ===== --}}
    @if($vehicle->reservations->count() > 0)
    <div class="section">
        <div class="section-title">📅 Histórico de Reservas ({{ $totalReservations }})</div>
        <table class="data">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Cliente</th>
                    <th>Período</th>
                    <th class="text-center">Dias</th>
                    <th class="text-right">Diária</th>
                    <th class="text-right">Valor Total</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->reservations->sortByDesc('created_at') as $r)
                <tr>
                    <td style="font-weight:bold;">#{{ $r->id }}</td>
                    <td>{{ $r->customer->name ?? '—' }}</td>
                    <td>{{ $r->pickup_date?->format('d/m/Y') }} a {{ $r->return_date?->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $r->total_days }}</td>
                    <td class="text-right">R$ {{ number_format((float)($r->daily_rate ?? 0), 2, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:bold;">R$ {{ number_format((float)($r->total ?? 0), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php $rsv = $r->status instanceof \BackedEnum ? $r->status->value : $r->status; @endphp
                        <span class="badge badge-purple">{{ ucfirst($rsv) }}</span>
                    </td>
                </tr>
                @endforeach
                <tr style="background:#fef9c3; font-weight:bold;">
                    <td colspan="5">Total Reservas</td>
                    <td class="text-right">R$ {{ number_format($revenueReservations, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ===== ORDENS DE SERVIÇO / MANUTENÇÕES ===== --}}
    @if($vehicle->serviceOrders->count() > 0)
    <div class="section">
        <div class="section-title">🔧 Ordens de Serviço / Manutenções ({{ $totalServiceOrders }})</div>
        <table class="data">
            <thead>
                <tr>
                    <th>OS #</th>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->serviceOrders->sortByDesc('created_at') as $os)
                <tr>
                    <td style="font-weight:bold;">#{{ $os->id }}</td>
                    <td>{{ $os->created_at?->format('d/m/Y') }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($os->description ?? $os->notes ?? '—', 80) }}</td>
                    <td class="text-right" style="font-weight:bold; color:#dc2626;">R$ {{ number_format((float)($os->total ?? 0), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php $osv = $os->status instanceof \BackedEnum ? $os->status->value : $os->status; @endphp
                        <span class="badge badge-gray">{{ ucfirst($osv ?? '—') }}</span>
                    </td>
                </tr>
                @endforeach
                <tr style="background:#fee2e2; font-weight:bold;">
                    <td colspan="3">Total OS / Manutenções</td>
                    <td class="text-right">R$ {{ number_format($expensesOS, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ===== MULTAS ===== --}}
    @if($vehicle->fines->count() > 0)
    <div class="section">
        <div class="section-title">⚠️ Multas de Trânsito ({{ $totalFines }})</div>
        <table class="data">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th class="text-right">Valor</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vehicle->fines->sortByDesc('infraction_date') as $fine)
                <tr>
                    <td>{{ $fine->infraction_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>{{ $fine->description ?? '—' }}</td>
                    <td class="text-right" style="font-weight:bold; color:#dc2626;">R$ {{ number_format((float)($fine->amount ?? 0), 2, ',', '.') }}</td>
                    <td class="text-center"><span class="badge badge-red">{{ ucfirst($fine->status ?? '—') }}</span></td>
                </tr>
                @endforeach
                <tr style="background:#fee2e2; font-weight:bold;">
                    <td colspan="2">Total Multas</td>
                    <td class="text-right">R$ {{ number_format($expensesFines, 2, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    {{-- ===== RESUMO FINANCEIRO ===== --}}
    <div class="section">
        <div class="section-title">💰 Resumo Financeiro</div>
        <table class="info-table" style="border: 1px solid #e5e7eb;">
            <tr style="background:#dcfce7;">
                <td class="info-label" style="width:50%;">Receita de Contratos:</td>
                <td class="info-value text-right" style="color:#059669; font-weight:bold;">R$ {{ number_format($revenueContracts, 2, ',', '.') }}</td>
            </tr>
            <tr style="background:#ede9fe;">
                <td class="info-label">Receita de Reservas:</td>
                <td class="info-value text-right" style="color:#5b21b6; font-weight:bold;">R$ {{ number_format($revenueReservations, 2, ',', '.') }}</td>
            </tr>
            <tr style="background:#d1fae5; border-top: 2px solid #059669;">
                <td class="info-label" style="font-size:10px;"><strong>RECEITA TOTAL</strong></td>
                <td class="info-value text-right" style="color:#059669; font-weight:bold; font-size:12px;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</td>
            </tr>
            <tr><td colspan="2" style="padding:3px;"></td></tr>
            <tr style="background:#fef2f2;">
                <td class="info-label">Ordens de Serviço:</td>
                <td class="info-value text-right" style="color:#dc2626;">R$ {{ number_format($expensesOS, 2, ',', '.') }}</td>
            </tr>
            <tr style="background:#fef2f2;">
                <td class="info-label">Multas:</td>
                <td class="info-value text-right" style="color:#dc2626;">R$ {{ number_format($expensesFines, 2, ',', '.') }}</td>
            </tr>
            <tr style="background:#fef2f2;">
                <td class="info-label">Seguro:</td>
                <td class="info-value text-right" style="color:#dc2626;">R$ {{ number_format($expensesInsurance, 2, ',', '.') }}</td>
            </tr>
            <tr style="background:#fee2e2; border-top: 2px solid #dc2626;">
                <td class="info-label" style="font-size:10px;"><strong>DESPESA TOTAL</strong></td>
                <td class="info-value text-right" style="color:#dc2626; font-weight:bold; font-size:12px;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</td>
            </tr>
            <tr><td colspan="2" style="padding:3px;"></td></tr>
            <tr style="background: {{ $profit >= 0 ? '#d1fae5' : '#fee2e2' }}; border-top: 3px solid {{ $profit >= 0 ? '#059669' : '#dc2626' }};">
                <td class="info-label" style="font-size:11px;"><strong>LUCRO LÍQUIDO</strong></td>
                <td class="info-value text-right" style="color:{{ $profit >= 0 ? '#059669' : '#dc2626' }}; font-weight:bold; font-size:14px;">R$ {{ number_format($profit, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ===== RODAPÉ ===== --}}
    <div class="footer">
        <strong>{{ $company['name'] }}</strong>
        @if($company['cnpj']) &bull; CNPJ: {{ $company['cnpj'] }} @endif
        @if($company['phone']) &bull; Tel.: {{ $company['phone'] }} @endif
        @if($company['email']) &bull; {{ $company['email'] }} @endif
        <br>
        {{ $company['footer'] }}
        <br>
        Gerado em: {{ now()->format('d/m/Y \à\s H:i:s') }}
    </div>

</body>
</html>
