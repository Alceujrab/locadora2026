<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Vistoria #{{ $inspection->id }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 20px 30px; }
        h1 { font-size: 18px; color: #2563eb; margin: 0; }
        h2 { font-size: 12px; color: #555; font-weight: normal; margin: 0; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 10px; margin-bottom: 15px; }
        .header-table, .info-table, .items { width: 100%; }
        .header-table td, .info-table td { border: none; padding: 3px 5px; vertical-align: top; }
        .section { margin-bottom: 15px; }
        .section-title { font-size: 13px; font-weight: bold; color: #2563eb; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-bottom: 8px; }
        .info-label { font-weight: bold; color: #555; width: 150px; }
        table.items { border-collapse: collapse; margin-top: 5px; }
        table.items th { background: #f5f5f5; border: 1px solid #ddd; padding: 5px 8px; text-align: left; font-size: 10px; text-transform: uppercase; color: #555; }
        table.items td { border: 1px solid #ddd; padding: 5px 8px; }
        .photo-grid { margin-top: 6px; }
        .photo-item { display: inline-block; width: 140px; margin: 0 8px 8px 0; vertical-align: top; }
        .photo-item img { width: 140px; height: 105px; object-fit: cover; border: 1px solid #ddd; padding: 3px; background: #fff; }
        .photo-caption { font-size: 9px; color: #666; margin-top: 3px; text-align: center; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; color: #fff; font-weight: bold; }
        .badge-warning { background: #f59e0b; }
        .badge-success { background: #16a34a; }
        .badge-info { background: #2563eb; }
        .signature-img { max-width: 220px; max-height: 90px; margin-top: 8px; }
        .signature-line { border-bottom: 1px solid #333; width: 220px; margin: 40px auto 0; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 8px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 80px;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-width: 70px; max-height: 70px;" alt="Logo">
                    @endif
                </td>
                <td>
                    <h1>VISTORIA #{{ $inspection->id }}</h1>
                    <h2>{{ $inspection->contract?->branch?->name ?? 'Elite Locadora' }}</h2>
                    <p style="font-size: 10px; color: #888; margin-top: 2px;">Emissao: {{ now()->format('d/m/Y H:i') }}</p>
                </td>
                <td style="text-align: right; width: 220px; font-size: 9px; color: #777;">
                    <p>Cliente: {{ $inspection->contract?->customer?->name ?? 'Nao vinculado' }}</p>
                    <p>Contrato: {{ $inspection->contract?->contract_number ?? '-' }}</p>
                    <p>Tipo: {{ $inspection->type->label() }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DADOS DA VISTORIA</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Veiculo:</td>
                <td>{{ $inspection->vehicle?->plate ?? '-' }} - {{ $inspection->vehicle?->brand ?? '' }} {{ $inspection->vehicle?->model ?? '' }}</td>
                <td class="info-label">Data/Hora:</td>
                <td>{{ $inspection->inspection_date?->format('d/m/Y H:i') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Quilometragem:</td>
                <td>{{ number_format((int) $inspection->mileage, 0, ',', '.') }} km</td>
                <td class="info-label">Combustivel:</td>
                <td>{{ (int) $inspection->fuel_level }}%</td>
            </tr>
            <tr>
                <td class="info-label">Condicao Geral:</td>
                <td>{{ ucfirst($inspection->overall_condition) }}</td>
                <td class="info-label">Status:</td>
                <td>
                    <span class="badge {{ $inspection->status === 'finalizado' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($inspection->status) }}</span>
                </td>
            </tr>
            <tr>
                <td class="info-label">Responsavel:</td>
                <td>{{ $inspection->inspector?->name ?? '-' }}</td>
                <td class="info-label">Assinada:</td>
                <td><span class="badge {{ $inspection->isSigned() ? 'badge-success' : 'badge-info' }}">{{ $inspection->isSigned() ? 'Sim' : 'Pendente' }}</span></td>
            </tr>
        </table>
    </div>

    @if($inspection->notes)
    <div class="section">
        <div class="section-title">OBSERVACOES</div>
        <p>{{ $inspection->notes }}</p>
    </div>
    @endif

    <div class="section">
        <div class="section-title">ITENS INSPECIONADOS</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Item</th>
                    <th>Condicao</th>
                    <th>Descricao da Avaria</th>
                    <th style="text-align:right">Custo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inspection->items as $item)
                <tr>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->item_name }}</td>
                    <td>{{ ucfirst($item->condition) }}</td>
                    <td>{{ $item->damage_description ?: '-' }}</td>
                    <td style="text-align:right">{{ $item->damage_value ? 'R$ ' . number_format((float) $item->damage_value, 2, ',', '.') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; color:#777;">Nenhum item detalhado informado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @php $hasPhotos = collect($itemPhotos ?? [])->flatten(1)->isNotEmpty(); @endphp
    @if($hasPhotos)
    <div class="section">
        <div class="section-title">FOTOS DA VISTORIA</div>
        @foreach($inspection->items as $item)
            @if(!empty($itemPhotos[$item->id]))
                <div style="margin-bottom: 10px;">
                    <p style="font-size: 10px; font-weight: bold; color: #444; margin-bottom: 5px;">
                        {{ $item->category }} - {{ $item->item_name }}
                    </p>
                    <div class="photo-grid">
                        @foreach($itemPhotos[$item->id] as $photo)
                            <div class="photo-item">
                                <img src="{{ $photo['base64'] }}" alt="Foto do item {{ $item->item_name }}">
                                <div class="photo-caption">{{ basename($photo['path']) }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    @endif

    <div class="section" style="margin-top: 30px;">
        <div class="section-title">ASSINATURA DO CLIENTE</div>
        @if(!empty($signatureBase64))
            <div style="text-align:center;">
                <img src="{{ $signatureBase64 }}" class="signature-img" alt="Assinatura">
                <p style="font-size: 10px; color: #666; margin-top: 5px;">{{ $inspection->contract?->customer?->name ?? 'Cliente' }}</p>
                <p style="font-size: 9px; color: #2563eb;">{{ $inspection->signed_at?->format('d/m/Y H:i') }} | IP: {{ $inspection->signature_ip }}</p>
                @if($inspection->signature_latitude)
                    <p style="font-size: 8px; color: #6b7280;">GPS: {{ $inspection->signature_latitude }}, {{ $inspection->signature_longitude }}</p>
                @endif
                <p style="font-size: 8px; color: #6b7280; word-break: break-all;">Hash: {{ $inspection->signature_hash }}</p>
            </div>
        @else
            <div style="text-align:center;">
                <div class="signature-line"></div>
                <p style="font-size: 10px; color: #666; margin-top: 5px;">Assinatura do Cliente</p>
                <p style="font-size: 9px; color: #999;">Pendente</p>
            </div>
        @endif
    </div>

    <div class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i:s') }} | Elite Locadora | Vistoria #{{ $inspection->id }}
    </div>
</body>
</html>