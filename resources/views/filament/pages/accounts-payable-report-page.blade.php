@php use App\Models\AccountPayable; @endphp

<x-filament-panels::page>
    @if(isset($error))
        <div style="background-color: #fee2e2; border: 1px solid #fecaca; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
            {{ $error }}
        </div>
    @endif

    <!-- Filters Section -->
    <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
        <h3 style="margin-top: 0; color: #1e293b;">Filtros</h3>
        <form action="{{ request()->url() }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Data Inicial</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Data Final</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Status</label>
                <select name="status" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                    <option value="">-- Todos --</option>
                    @foreach(['pendente' => 'Pendente', 'pago' => 'Pago', 'cancelado' => 'Cancelado'] as $value => $label)
                        <option value="{{ $value }}" {{ $filters['status'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Categoria</label>
                <select name="category" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                    <option value="">-- Todas --</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ $filters['category'] === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Fornecedor</label>
                <select name="supplier_id" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                    <option value="">-- Todos --</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ $filters['supplier_id'] == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; color: #334155; margin-bottom: 5px; font-size: 14px;">Filial</label>
                <select name="branch_id" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;">
                    <option value="">-- Todas --</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $filters['branch_id'] == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 10px; align-items: flex-end;">
                <button type="submit" style="flex: 1; background-color: #3b82f6; color: white; font-weight: 600; padding: 8px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Filtrar</button>
                <a href="{{ request()->url() }}" style="flex: 1; background-color: #6b7280; color: white; font-weight: 600; padding: 8px; border-radius: 4px; cursor: pointer; text-align: center; text-decoration: none; font-size: 14px;">Limpar</a>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: linear-gradient(135deg, #ef4444 0%, #991b1b 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Total de Contas a Pagar</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $totalCount }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">R$ {{ number_format($totalAmount, 2, ',', '.') }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Contas Pendentes</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $pendingCount }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">R$ {{ number_format($pendingAmount, 2, ',', '.') }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #dc2626 0%, #7f1d1d 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Contas Vencidas</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $overdueCount }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">R$ {{ number_format($overdueAmount, 2, ',', '.') }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Contas Pagas</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $paidCount }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">R$ {{ number_format($paidAmount, 2, ',', '.') }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #6b7280 0%, #374151 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Contas Canceladas</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $cancelledCount }}</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">R$ {{ number_format($cancelledAmount, 2, ',', '.') }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="font-size: 12px; opacity: 0.9; margin-bottom: 8px;">Taxa de Pagamento</div>
            <div style="font-size: 28px; font-weight: bold;">{{ $totalAmount > 0 ? number_format(($paidAmount / $totalAmount) * 100, 1) : 0 }}%</div>
            <div style="font-size: 14px; opacity: 0.9; margin-top: 5px;">Pagas vs Total</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
            <h3 style="margin-top: 0; color: #1e293b;">Distribuição por Status</h3>
            <canvas id="statusChart" style="max-height: 300px;"></canvas>
        </div>

        @if(count($categoryData['labels']) > 0)
            <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px;">
                <h3 style="margin-top: 0; color: #1e293b;">Despesas por Categoria</h3>
                <canvas id="categoryChart" style="max-height: 300px;"></canvas>
            </div>
        @endif
    </div>

    <!-- Export Buttons -->
    <div style="display: flex; gap: 10px; margin-bottom: 20px;">
        <button onclick="exportPdf()" style="background-color: #dc2626; color: white; font-weight: 600; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">📥 Exportar PDF</button>
        <button onclick="exportExcel()" style="background-color: #16a34a; color: white; font-weight: 600; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">📊 Exportar Excel</button>
    </div>

    <!-- Data Table -->
    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead style="background-color: #f1f5f9; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #334155;">Descrição</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #334155;">Fornecedor</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #334155;">Vencimento</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #334155;">Categoria</th>
                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #334155;">Valor</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #334155;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $record)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; color: #1e293b;">{{ $record->description }}</td>
                        <td style="padding: 12px; color: #1e293b;">{{ $record->supplier->name ?? 'N/A' }}</td>
                        <td style="padding: 12px; color: #1e293b;">{{ $record->due_date->format('d/m/Y') }}</td>
                        <td style="padding: 12px; color: #1e293b;">{{ ucfirst($record->category) }}</td>
                        <td style="padding: 12px; text-align: right; color: #1e293b; font-weight: 600;">R$ {{ number_format($record->amount, 2, ',', '.') }}</td>
                        <td style="padding: 12px;">
                            @php
                                $statusColors = [
                                    'pendente' => '#f59e0b',
                                    'pago' => '#10b981',
                                    'cancelado' => '#6b7280',
                                ];
                                $color = $statusColors[$record->status] ?? '#6b7280';
                            @endphp
                            <span style="background-color: {{ $color }}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">{{ ucfirst($record->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">Nenhuma conta a pagar encontrada</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($records->hasPages())
            <div style="padding: 15px; border-top: 1px solid #e2e8f0; display: flex; justify-content: center;">
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($statusData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($statusData['data']) !!},
                    backgroundColor: {!! json_encode($statusData['colors']) !!},
                    borderColor: 'white',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        @if(count($categoryData['labels']) > 0)
            // Category Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($categoryData['labels']) !!},
                    datasets: [{
                        label: 'Valor (R$)',
                        data: {!! json_encode($categoryData['data']) !!},
                        backgroundColor: {!! json_encode($categoryData['colors']) !!},
                        borderColor: 'white',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        @endif

        function exportPdf() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '/export/accounts-payable/pdf?' + params.toString();
        }

        function exportExcel() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = '/export/accounts-payable/excel?' + params.toString();
        }
    </script>
</x-filament-panels::page>
