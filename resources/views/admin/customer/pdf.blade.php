<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Ficha do Cliente - {{ $customer->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1a365d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 22px;
            color: #1a365d;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 12px;
            color: #666;
        }
        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #1a365d;
            color: #fff;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            border-radius: 3px;
        }
        .grid {
            width: 100%;
            border-collapse: collapse;
        }
        .grid td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .grid td.label {
            font-weight: bold;
            color: #4a5568;
            width: 35%;
            background-color: #f7fafc;
        }
        .grid td.value {
            width: 65%;
        }
        .grid-2col {
            width: 100%;
            border-collapse: collapse;
        }
        .grid-2col td {
            padding: 4px 8px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            width: 25%;
        }
        .grid-2col td.label {
            font-weight: bold;
            color: #4a5568;
            background-color: #f7fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            color: #fff;
        }
        .badge-pf { background-color: #3182ce; }
        .badge-pj { background-color: #805ad5; }
        .badge-blocked { background-color: #e53e3e; }
        .badge-active { background-color: #38a169; }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .signature-area {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 60%;
            margin: 40px auto 6px;
        }
        .signature-text {
            text-align: center;
            font-size: 11px;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FICHA CADASTRAL DO CLIENTE</h1>
        <p>
            @if($customer->branch)
                {{ $customer->branch->name }} |
            @endif
            Emitido em {{ now()->format('d/m/Y H:i') }}
        </p>
    </div>

    {{-- Dados Pessoais --}}
    <div class="section">
        <div class="section-title">DADOS PESSOAIS</div>
        <table class="grid">
            <tr>
                <td class="label">Nome</td>
                <td class="value">{{ $customer->name }}</td>
            </tr>
            <tr>
                <td class="label">Tipo</td>
                <td class="value">
                    @if($customer->type?->value === 'pf' || $customer->type === 'pf')
                        <span class="badge badge-pf">Pessoa Física</span>
                    @else
                        <span class="badge badge-pj">Pessoa Jurídica</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">CPF/CNPJ</td>
                <td class="value">{{ $customer->cpf_cnpj ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">RG/IE</td>
                <td class="value">{{ $customer->rg ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Data de Nascimento</td>
                <td class="value">{{ $customer->birth_date ? $customer->birth_date->format('d/m/Y') : '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Dados PJ (se aplicável) --}}
    @if($customer->company_name || $customer->state_registration)
    <div class="section">
        <div class="section-title">DADOS PESSOA JURÍDICA</div>
        <table class="grid">
            <tr>
                <td class="label">Razão Social</td>
                <td class="value">{{ $customer->company_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Inscrição Estadual</td>
                <td class="value">{{ $customer->state_registration ?? '—' }}</td>
            </tr>
            @if($customer->responsible_name)
            <tr>
                <td class="label">Responsável</td>
                <td class="value">{{ $customer->responsible_name }} {{ $customer->responsible_cpf ? '(CPF: '.$customer->responsible_cpf.')' : '' }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    {{-- Contato --}}
    <div class="section">
        <div class="section-title">CONTATO</div>
        <table class="grid">
            <tr>
                <td class="label">E-mail</td>
                <td class="value">{{ $customer->email ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Telefone</td>
                <td class="value">{{ $customer->phone ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">WhatsApp</td>
                <td class="value">{{ $customer->whatsapp ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- CNH --}}
    @if($customer->cnh_number)
    <div class="section">
        <div class="section-title">CARTEIRA NACIONAL DE HABILITAÇÃO</div>
        <table class="grid">
            <tr>
                <td class="label">Nº CNH</td>
                <td class="value">{{ $customer->cnh_number }}</td>
            </tr>
            <tr>
                <td class="label">Categoria</td>
                <td class="value">{{ $customer->cnh_category ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Validade</td>
                <td class="value">{{ $customer->cnh_expiry ? $customer->cnh_expiry->format('d/m/Y') : '—' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Endereço --}}
    <div class="section">
        <div class="section-title">ENDEREÇO</div>
        <table class="grid">
            <tr>
                <td class="label">Logradouro</td>
                <td class="value">
                    {{ $customer->address_street ?? '—' }}{{ $customer->address_number ? ', ' . $customer->address_number : '' }}
                    {{ $customer->address_complement ? ' - ' . $customer->address_complement : '' }}
                </td>
            </tr>
            <tr>
                <td class="label">Bairro</td>
                <td class="value">{{ $customer->address_neighborhood ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Cidade/UF</td>
                <td class="value">{{ $customer->address_city ?? '—' }}{{ $customer->address_state ? '/' . $customer->address_state : '' }}</td>
            </tr>
            <tr>
                <td class="label">CEP</td>
                <td class="value">{{ $customer->address_zip ?? '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- Contato de Emergência --}}
    @if($customer->emergency_contact_name)
    <div class="section">
        <div class="section-title">CONTATO DE EMERGÊNCIA</div>
        <table class="grid">
            <tr>
                <td class="label">Nome</td>
                <td class="value">{{ $customer->emergency_contact_name }}</td>
            </tr>
            <tr>
                <td class="label">Telefone</td>
                <td class="value">{{ $customer->emergency_contact_phone ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Relação</td>
                <td class="value">{{ $customer->emergency_contact_relation ?? '—' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Status --}}
    <div class="section">
        <div class="section-title">STATUS</div>
        <table class="grid">
            <tr>
                <td class="label">Situação</td>
                <td class="value">
                    @if($customer->is_blocked)
                        <span class="badge badge-blocked">BLOQUEADO</span>
                    @else
                        <span class="badge badge-active">ATIVO</span>
                    @endif
                </td>
            </tr>
            @if($customer->is_blocked && $customer->blocked_reason)
            <tr>
                <td class="label">Motivo Bloqueio</td>
                <td class="value">{{ $customer->blocked_reason }}</td>
            </tr>
            @endif
            @if($customer->notes)
            <tr>
                <td class="label">Observações</td>
                <td class="value">{{ $customer->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Área de assinatura --}}
    <div class="signature-area">
        <div class="signature-line"></div>
        <p class="signature-text">Assinatura do Cliente</p>
    </div>

    <div class="footer">
        @if($customer->branch)
            {{ $customer->branch->name }} —
        @endif
        Documento gerado pelo sistema Elite Locadora em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
