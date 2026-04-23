@php
    /** @var \App\Models\FineTraffic $fine */
    $driver = [
        'name'    => $fine->driver_name,
        'cpf'     => $fine->driver_cpf,
        'rg'      => $fine->driver_rg,
        'phone'   => $fine->driver_phone,
        'email'   => $fine->driver_email,
        'cnh'     => $fine->driver_cnh_number,
        'cnh_exp' => $fine->driver_cnh_expires_at?->format('d/m/Y'),
        'zip'     => $fine->driver_zipcode,
        'street'  => $fine->driver_address,
        'number'  => $fine->driver_address_number,
        'comp'    => $fine->driver_address_complement,
        'nb'      => $fine->driver_neighborhood,
        'city'    => $fine->driver_city,
        'state'   => $fine->driver_state,
    ];
    $vehicle = $fine->vehicle;
    $owner   = $fine->customer ?? $fine->contract?->customer;
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>FICI - Formulário de Identificação do Condutor Infrator</title>
    <style>
        @page { margin: 18mm 15mm; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10pt; color: #111; margin: 0; }
        h1 { font-size: 13pt; text-align: center; margin: 0 0 4px; text-transform: uppercase; }
        h2 { font-size: 10pt; text-align: center; margin: 0 0 12px; font-weight: normal; color: #333; }
        .hdr { width: 100%; border-bottom: 2px solid #111; padding-bottom: 6px; margin-bottom: 10px; }
        .hdr td { vertical-align: middle; }
        .hdr .logo img { max-height: 55px; }
        .hdr .co { font-size: 9pt; line-height: 1.35; text-align: right; }
        .sec { border: 1px solid #222; margin-bottom: 8px; }
        .sec-title { background: #222; color: #fff; padding: 4px 8px; font-weight: bold; font-size: 10pt; text-transform: uppercase; letter-spacing: 0.03em; }
        .sec-body { padding: 8px; }
        table.grid { width: 100%; border-collapse: collapse; }
        table.grid td { padding: 4px 6px; vertical-align: top; }
        .lbl { font-size: 8pt; color: #444; text-transform: uppercase; letter-spacing: 0.02em; display: block; margin-bottom: 2px; }
        .val { font-size: 10pt; font-weight: bold; border-bottom: 1px solid #888; padding-bottom: 2px; min-height: 14px; }
        .decl { font-size: 9.5pt; text-align: justify; line-height: 1.5; padding: 6px 2px; }
        .sign-row { width: 100%; margin-top: 25px; }
        .sign-row td { width: 50%; text-align: center; padding: 0 8px; vertical-align: bottom; }
        .sign-line { border-top: 1px solid #111; margin-top: 55px; padding-top: 3px; font-size: 9pt; }
        .footer-legal { font-size: 7.5pt; color: #555; text-align: center; margin-top: 14px; line-height: 1.35; }
        .check { font-size: 9pt; margin-top: 8px; }
        .check span { display: inline-block; border: 1px solid #111; width: 10px; height: 10px; margin-right: 4px; vertical-align: middle; }
    </style>
</head>
<body>

<table class="hdr">
    <tr>
        <td class="logo" style="width: 35%;">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Logo">
            @else
                <strong style="font-size: 12pt;">{{ $company['name'] }}</strong>
            @endif
        </td>
        <td class="co">
            <strong>{{ $company['name'] }}</strong><br>
            @if($company['cnpj']) CNPJ: {{ $company['cnpj'] }}<br>@endif
            @if($company['address']) {{ $company['address'] }}@if($company['city']), {{ $company['city'] }}/{{ $company['state'] }}@endif<br>@endif
            @if($company['phone']) Tel.: {{ $company['phone'] }} @endif
            @if($company['email']) - {{ $company['email'] }} @endif
        </td>
    </tr>
</table>

<h1>Formulário de Identificação do Condutor Infrator</h1>
<h2>(CONTRAN — Resolução nº 918/2022, art. 11 / Art. 257, §7º do CTB)</h2>

{{-- ============ AUTO DE INFRAÇÃO ============ --}}
<div class="sec">
    <div class="sec-title">1. Dados do Auto de Infração</div>
    <div class="sec-body">
        <table class="grid">
            <tr>
                <td style="width: 40%;"><span class="lbl">Nº do Auto de Infração (AIT)</span><span class="val">{{ $fine->auto_infraction_number ?: '—' }}</span></td>
                <td style="width: 30%;"><span class="lbl">Código da Infração</span><span class="val">{{ $fine->fine_code ?: '—' }}</span></td>
                <td style="width: 30%;"><span class="lbl">Valor (R$)</span><span class="val">{{ number_format((float)$fine->amount, 2, ',', '.') }}</span></td>
            </tr>
            <tr>
                <td><span class="lbl">Data da Infração</span><span class="val">{{ $fine->fine_date?->format('d/m/Y') ?: '—' }}</span></td>
                <td><span class="lbl">Data da Notificação</span><span class="val">{{ $fine->notification_date?->format('d/m/Y') ?: '—' }}</span></td>
                <td><span class="lbl">Vencimento</span><span class="val">{{ $fine->due_date?->format('d/m/Y') ?: '—' }}</span></td>
            </tr>
            <tr>
                <td colspan="3"><span class="lbl">Descrição da Infração / Local</span><span class="val" style="white-space: normal; min-height: 28px;">{{ $fine->description ?: '—' }}</span></td>
            </tr>
        </table>
    </div>
</div>

{{-- ============ VEÍCULO ============ --}}
<div class="sec">
    <div class="sec-title">2. Dados do Veículo</div>
    <div class="sec-body">
        <table class="grid">
            <tr>
                <td style="width: 25%;"><span class="lbl">Placa</span><span class="val">{{ $vehicle?->plate ?: '—' }}</span></td>
                <td style="width: 25%;"><span class="lbl">RENAVAM</span><span class="val">{{ $vehicle?->renavam ?: '—' }}</span></td>
                <td style="width: 25%;"><span class="lbl">Chassi</span><span class="val">{{ $vehicle?->chassis ?: '—' }}</span></td>
                <td style="width: 25%;"><span class="lbl">Ano Fab./Mod.</span><span class="val">{{ $vehicle?->year_manufacture ?? '—' }}/{{ $vehicle?->year_model ?? '—' }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lbl">Marca/Modelo</span><span class="val">{{ trim(($vehicle?->brand ?? '').' '.($vehicle?->model ?? '')) ?: '—' }}</span></td>
                <td><span class="lbl">Cor</span><span class="val">{{ $vehicle?->color ?: '—' }}</span></td>
                <td><span class="lbl">Categoria</span><span class="val">{{ $vehicle?->category ?: '—' }}</span></td>
            </tr>
        </table>
    </div>
</div>

{{-- ============ PROPRIETÁRIO ============ --}}
<div class="sec">
    <div class="sec-title">3. Proprietário do Veículo (Locadora)</div>
    <div class="sec-body">
        <table class="grid">
            <tr>
                <td style="width: 60%;"><span class="lbl">Razão Social</span><span class="val">{{ $company['name'] }}</span></td>
                <td style="width: 40%;"><span class="lbl">CNPJ</span><span class="val">{{ $company['cnpj'] ?: '—' }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lbl">Endereço</span><span class="val">{{ trim(($company['address'] ?? '').' — '.($company['city'] ?? '').'/'.($company['state'] ?? '').' — CEP '.($company['zip'] ?? ''), ' —/') }}</span></td>
            </tr>
        </table>
    </div>
</div>

{{-- ============ REAL INFRATOR / CONDUTOR ============ --}}
<div class="sec">
    <div class="sec-title">4. Real Infrator / Condutor do Veículo</div>
    <div class="sec-body">
        <table class="grid">
            <tr>
                <td colspan="3"><span class="lbl">Nome Completo</span><span class="val">{{ $driver['name'] }}</span></td>
            </tr>
            <tr>
                <td style="width: 34%;"><span class="lbl">CPF</span><span class="val">{{ $driver['cpf'] }}</span></td>
                <td style="width: 33%;"><span class="lbl">RG / Órgão Emissor</span><span class="val">{{ $driver['rg'] ?: '—' }}</span></td>
                <td style="width: 33%;"><span class="lbl">Nº da CNH</span><span class="val">{{ $driver['cnh'] ?: '—' }}</span></td>
            </tr>
            <tr>
                <td><span class="lbl">Validade CNH</span><span class="val">{{ $driver['cnh_exp'] ?: '—' }}</span></td>
                <td><span class="lbl">Telefone</span><span class="val">{{ $driver['phone'] ?: '—' }}</span></td>
                <td><span class="lbl">E-mail</span><span class="val">{{ $driver['email'] ?: '—' }}</span></td>
            </tr>
            <tr>
                <td colspan="2"><span class="lbl">Endereço (Rua/Av.)</span><span class="val">{{ $driver['street'] }}, {{ $driver['number'] ?: 'S/N' }}{{ $driver['comp'] ? ' — '.$driver['comp'] : '' }}</span></td>
                <td><span class="lbl">Bairro</span><span class="val">{{ $driver['nb'] ?: '—' }}</span></td>
            </tr>
            <tr>
                <td><span class="lbl">Cidade</span><span class="val">{{ $driver['city'] ?: '—' }}</span></td>
                <td><span class="lbl">UF</span><span class="val">{{ $driver['state'] ?: '—' }}</span></td>
                <td><span class="lbl">CEP</span><span class="val">{{ $driver['zip'] ?: '—' }}</span></td>
            </tr>
        </table>
    </div>
</div>

{{-- ============ DECLARAÇÃO ============ --}}
<div class="sec">
    <div class="sec-title">5. Declaração / Indicação de Condutor</div>
    <div class="sec-body">
        <p class="decl">
            Em cumprimento ao disposto no <strong>art. 257, §7º do Código de Trânsito Brasileiro</strong>
            (Lei nº 9.503/1997) e na <strong>Resolução CONTRAN nº 918/2022</strong>, o proprietário do veículo
            acima qualificado <strong>INDICA</strong> como <strong>REAL INFRATOR/CONDUTOR</strong> do veículo,
            no momento em que foi cometida a infração descrita no Auto de Infração em referência, a pessoa
            qualificada no item 4 deste formulário.
        </p>
        <p class="decl">
            O <strong>condutor indicado</strong>, ao assinar o presente formulário, reconhece a
            responsabilidade pela infração de trânsito acima descrita e autoriza o registro dos
            respectivos pontos em seu prontuário, bem como a transferência da pontuação e responsabilização
            por eventual suspensão do direito de dirigir, nos termos da legislação vigente.
        </p>
        <p class="decl" style="font-size: 8.5pt; color: #222;">
            <strong>Documentos obrigatórios anexos</strong> (conforme Res. CONTRAN 918/2022, art. 11, §2º):
            cópia da <strong>CNH</strong> ou documento de identificação oficial com foto do condutor e
            comprovante de endereço. <strong>A assinatura do condutor infrator e do proprietário deve ser
            reconhecida em cartório</strong> (firma reconhecida por autenticidade ou semelhança).
        </p>
        <div class="check">
            <span></span> Cópia da CNH (ou documento de identificação oficial com foto) anexa &nbsp;&nbsp;
            <span></span> Comprovante de endereço do condutor anexo
        </div>
    </div>
</div>

{{-- ============ ASSINATURAS ============ --}}
<table class="sign-row">
    <tr>
        <td>
            <div class="sign-line">
                <strong>{{ $company['name'] }}</strong><br>
                Proprietário do veículo / Representante Legal<br>
                CNPJ: {{ $company['cnpj'] ?: '____________________________' }}
            </div>
        </td>
        <td>
            <div class="sign-line">
                <strong>{{ $driver['name'] }}</strong><br>
                Condutor Infrator / Real Infrator<br>
                CPF: {{ $driver['cpf'] }}
            </div>
        </td>
    </tr>
</table>

<p style="margin-top: 25px; font-size: 9pt;">
    Local e data: ____________________________________, _______ de _________________________ de {{ now()->format('Y') }}.
</p>

<p class="footer-legal">
    * A indicação do condutor infrator deve ser protocolada no órgão autuador no prazo de <strong>30 (trinta) dias</strong>
    contados da ciência da notificação da autuação, sob pena de ser a multa aplicada ao proprietário do veículo
    (art. 257, §8º do CTB). Formulário gerado em {{ now()->format('d/m/Y H:i') }}.
</p>

</body>
</html>
