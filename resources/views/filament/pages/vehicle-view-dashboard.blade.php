<x-filament-panels::page>
    <div class="space-y-6">
        {{-- HEADER: Dados do Veiculo --}}
        <div style="background: linear-gradient(to right, #111827, #1f2937); border-radius: 0.75rem; padding: 1.5rem; color: #fff; display: flex; align-items: center; gap: 1.5rem;">
            @if($vehicle->cover_photo)
                <img src="{{ Storage::url($vehicle->cover_photo) }}" style="width: 8rem; height: 6rem; object-fit: cover; border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.3);" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" />
            @else
                <div style="width: 8rem; height: 6rem; background: #374151; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                    <x-heroicon-o-truck style="width: 3rem; height: 3rem; color: #6b7280;" />
                </div>
            @endif
            <div style="flex: 1;">
                <h2 style="font-size: 1.5rem; font-weight: 700; margin: 0;">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                <div style="display: flex; gap: 1rem; margin-top: 0.25rem; font-size: 0.875rem; color: #d1d5db; flex-wrap: wrap;">
                    <span style="background: #f59e0b; color: #000; font-weight: 700; padding: 0.125rem 0.75rem; border-radius: 0.25rem;">{{ $vehicle->plate }}</span>
                    <span>{{ $vehicle->year_manufacture }}/{{ $vehicle->year_model }}</span>
                    @if($vehicle->color)<span>{{ $vehicle->color }}</span>@endif
                    @if($vehicle->category)<span>{{ $vehicle->category->name }}</span>@endif
                    @if($vehicle->branch)<span>{{ $vehicle->branch->name }}</span>@endif
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 0.5rem; font-size: 0.75rem; color: #9ca3af; flex-wrap: wrap;">
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
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #9ca3af; text-transform: uppercase;">Status</div>
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
                <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; {{ $statusColors }}">
                    {{ $vehicle->status->label() }}
                </span>
            </div>
        </div>

        {{-- KPIs FINANCEIROS --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Receita Total</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #16a34a;">R$ {{ number_format($totalRevenue, 2, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">Contratos: R$ {{ number_format($revenueContracts, 2, ',', '.') }} | Reservas: R$ {{ number_format($revenueReservations, 2, ',', '.') }}</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Despesas Total</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #dc2626;">R$ {{ number_format($totalExpenses, 2, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">OS: R$ {{ number_format($expensesOS, 2, ',', '.') }} | Multas: R$ {{ number_format($expensesFines, 2, ',', '.') }}</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Lucro Liquido</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: {{ $profit >= 0 ? '#059669' : '#dc2626' }};">R$ {{ number_format($profit, 2, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">ROI: {{ number_format($roi, 1, ',', '.') }}%</div>
            </div>
            <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div style="font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">Diaria Media</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">R$ {{ number_format($avgDailyRate, 2, ',', '.') }}</div>
                <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">{{ $totalDaysRented }} dias locados</div>
            </div>
        </div>

        {{-- CONTADORES --}}
        <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.75rem;">
            @php
                $counters = [
                    ['value' => $totalContracts, 'label' => 'Contratos', 'color' => '#2563eb', 'bg' => 'rgba(37,99,235,0.1)', 'border' => 'rgba(37,99,235,0.3)'],
                    ['value' => $totalReservations, 'label' => 'Reservas', 'color' => '#4f46e5', 'bg' => 'rgba(79,70,229,0.1)', 'border' => 'rgba(79,70,229,0.3)'],
                    ['value' => $totalServiceOrders, 'label' => 'Ordens de Servico', 'color' => '#ea580c', 'bg' => 'rgba(234,88,12,0.1)', 'border' => 'rgba(234,88,12,0.3)'],
                    ['value' => $totalFines, 'label' => 'Multas', 'color' => '#dc2626', 'bg' => 'rgba(220,38,38,0.1)', 'border' => 'rgba(220,38,38,0.3)'],
                    ['value' => $totalInspections, 'label' => 'Vistorias', 'color' => '#7c3aed', 'bg' => 'rgba(124,58,237,0.1)', 'border' => 'rgba(124,58,237,0.3)'],
                ];
            @endphp
            @foreach($counters as $counter)
                <div style="background: {{ $counter['bg'] }}; border: 1px solid {{ $counter['border'] }}; border-radius: 0.5rem; padding: 0.75rem; text-align: center;">
                    <div style="font-size: 1.875rem; font-weight: 700; color: {{ $counter['color'] }};">{{ $counter['value'] }}</div>
                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $counter['label'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- CONTRATO ATIVO + MANUTENCAO --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
            @if($activeContract)
                <div style="background: rgba(37,99,235,0.08); border: 1px solid rgba(37,99,235,0.25); border-radius: 0.75rem; padding: 1.25rem;">
                    <h3 style="font-weight: 700; color: #2563eb; margin-bottom: 0.5rem;">📋 Contrato Ativo</h3>
                    <div style="font-size: 0.875rem; line-height: 1.75;">
                        <div><strong>Cliente:</strong> {{ $activeContract->customer?->name ?? '-' }}</div>
                        <div><strong>Periodo:</strong> {{ $activeContract->pickup_date?->format('d/m/Y') ?? '-' }} a {{ $activeContract->return_date?->format('d/m/Y') ?? '-' }}</div>
                        <div><strong>Valor:</strong> R$ {{ number_format((float)($activeContract->total ?? 0), 2, ',', '.') }}</div>
                    </div>
                </div>
            @else
                <div class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 style="font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">📋 Contrato Ativo</h3>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Nenhum contrato ativo no momento.</p>
                </div>
            @endif

            @if($nextMaintenance)
                <div style="background: rgba(234,179,8,0.08); border: 1px solid rgba(234,179,8,0.25); border-radius: 0.75rem; padding: 1.25rem;">
                    <h3 style="font-weight: 700; color: #ca8a04; margin-bottom: 0.5rem;">🔧 Proxima Manutencao</h3>
                    <div style="font-size: 0.875rem; line-height: 1.75;">
                        <div><strong>Tipo:</strong> {{ $nextMaintenance->type ?? '-' }}</div>
                        <div><strong>Data:</strong> {{ $nextMaintenance->due_date?->format('d/m/Y') ?? '-' }}</div>
                        <div><strong>Descricao:</strong> {{ $nextMaintenance->description ?? '-' }}</div>
                    </div>
                </div>
            @else
                <div class="fi-section rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                    <h3 style="font-weight: 700; color: #6b7280; margin-bottom: 0.5rem;">🔧 Manutencao</h3>
                    <p style="font-size: 0.875rem; color: #9ca3af;">Nenhuma manutencao pendente.</p>
                </div>
            @endif
        </div>

        {{-- HISTORICO DE LOCACOES --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
            <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06);">
                <h3 style="font-weight: 700; color: #374151; margin: 0;">📊 Historico de Locacoes (Contratos)</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.02); font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">
                            <th style="padding: 0.5rem 1rem; text-align: left;">Cliente</th>
                            <th style="padding: 0.5rem 1rem; text-align: left;">Periodo</th>
                            <th style="padding: 0.5rem 1rem; text-align: right;">Dias</th>
                            <th style="padding: 0.5rem 1rem; text-align: right;">Valor</th>
                            <th style="padding: 0.5rem 1rem; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicle->contracts->sortByDesc('created_at')->take(10) as $contract)
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 0.5rem 1rem; font-weight: 500;">{{ $contract->customer?->name ?? '-' }}</td>
                                <td style="padding: 0.5rem 1rem; color: #6b7280;">{{ $contract->pickup_date?->format('d/m') ?? '-' }} a {{ $contract->return_date?->format('d/m/Y') ?? '-' }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: right;">{{ $contract->total_days ?? '-' }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: right; font-weight: 500; color: #16a34a;">R$ {{ number_format((float)($contract->total ?? 0), 2, ',', '.') }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: center;">
                                    @php
                                        $cStatusColor = ($contract->status?->value ?? '') === 'ativo' ? 'background: #dcfce7; color: #15803d;' : 'background: #f3f4f6; color: #4b5563;';
                                    @endphp
                                    <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; {{ $cStatusColor }}">
                                        {{ $contract->status instanceof \BackedEnum ? $contract->status->label() : ($contract->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding: 1rem; text-align: center; color: #9ca3af;">Nenhum contrato registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ORDENS DE SERVICO --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
            <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06);">
                <h3 style="font-weight: 700; color: #374151; margin: 0;">🔧 Ordens de Servico</h3>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.02); font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">
                            <th style="padding: 0.5rem 1rem; text-align: left;">OS #</th>
                            <th style="padding: 0.5rem 1rem; text-align: left;">Data</th>
                            <th style="padding: 0.5rem 1rem; text-align: left;">Descricao</th>
                            <th style="padding: 0.5rem 1rem; text-align: right;">Valor</th>
                            <th style="padding: 0.5rem 1rem; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicle->serviceOrders->sortByDesc('created_at')->take(10) as $os)
                            <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                <td style="padding: 0.5rem 1rem; font-weight: 500;">#{{ $os->id }}</td>
                                <td style="padding: 0.5rem 1rem; color: #6b7280;">{{ $os->created_at?->format('d/m/Y') ?? '-' }}</td>
                                <td style="padding: 0.5rem 1rem;">{{ \Illuminate\Support\Str::limit($os->description ?? $os->notes ?? '-', 50) }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: right; font-weight: 500; color: #dc2626;">R$ {{ number_format((float)($os->total ?? 0), 2, ',', '.') }}</td>
                                <td style="padding: 0.5rem 1rem; text-align: center;">
                                    <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; background: #f3f4f6; color: #4b5563;">
                                        {{ $os->status instanceof \BackedEnum ? $os->status->label() : ($os->status ?? '-') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="padding: 1rem; text-align: center; color: #9ca3af;">Nenhuma OS registrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MULTAS --}}
        @if($totalFines > 0)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
                <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 style="font-weight: 700; color: #374151; margin: 0;">⚠️ Multas de Transito</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                        <thead>
                            <tr style="background: rgba(0,0,0,0.02); font-size: 0.75rem; color: #6b7280; text-transform: uppercase;">
                                <th style="padding: 0.5rem 1rem; text-align: left;">Data</th>
                                <th style="padding: 0.5rem 1rem; text-align: left;">Descricao</th>
                                <th style="padding: 0.5rem 1rem; text-align: right;">Valor</th>
                                <th style="padding: 0.5rem 1rem; text-align: center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicle->fines->sortByDesc('infraction_date')->take(10) as $fine)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <td style="padding: 0.5rem 1rem;">{{ $fine->infraction_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td style="padding: 0.5rem 1rem;">{{ $fine->description ?? '-' }}</td>
                                    <td style="padding: 0.5rem 1rem; text-align: right; font-weight: 500; color: #dc2626;">R$ {{ number_format((float)($fine->amount ?? 0), 2, ',', '.') }}</td>
                                    <td style="padding: 0.5rem 1rem; text-align: center;">
                                        <span style="padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; background: #f3f4f6; color: #4b5563;">{{ $fine->status ?? '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- GALERIA DE FOTOS --}}
        @if($vehicle->photos->count() > 0)
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="overflow: hidden;">
                <div style="background: rgba(0,0,0,0.03); padding: 0.75rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.06);">
                    <h3 style="font-weight: 700; color: #374151; margin: 0;">📸 Fotos do Veiculo</h3>
                </div>
                <div style="display: flex; gap: 0.75rem; padding: 1rem; overflow-x: auto;">
                    @foreach($vehicle->photos->take(8) as $photo)
                        <img src="{{ Storage::url($photo->path) }}" style="width: 10rem; height: 7rem; object-fit: cover; border-radius: 0.5rem; flex-shrink: 0;" alt="Foto #{{ $loop->iteration }}" />
                    @endforeach
                </div>
            </div>
        @endif

        {{-- VALORES DO VEICULO --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem;">
            @php
                $values = [
                    ['label' => 'Valor Compra', 'value' => $vehicle->purchase_value],
                    ['label' => 'Valor FIPE', 'value' => $vehicle->fipe_value],
                    ['label' => 'Valor Seguro', 'value' => $vehicle->insurance_value],
                    ['label' => 'Diaria Configurada', 'value' => $vehicle->daily_rate],
                ];
            @endphp
            @foreach($values as $item)
                <div class="fi-section rounded-xl bg-white p-4 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10" style="text-align: center;">
                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $item['label'] }}</div>
                    <div style="font-size: 1.125rem; font-weight: 700;">R$ {{ number_format((float)($item['value'] ?? 0), 2, ',', '.') }}</div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
