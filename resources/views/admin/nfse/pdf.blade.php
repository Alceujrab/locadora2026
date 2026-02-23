<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NFS-e {{ $nfse->numero }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; }
        .box { border: 1px solid #ccc; padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .box-title { font-weight: bold; font-size: 14px; margin-bottom: 8px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; font-size: 11px; }
        th { background-color: #f9f9f9; width: 30%; }
        .values-table th { width: auto; text-align: right; }
        .values-table td { text-align: right; font-weight: bold; }
        .footer { text-align: center; font-size: 10px; color: #777; margin-top: 30px; border-top: 1px solid #ccc; padding-top: 10px; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-weight: bold; color: white; background-color: #10b981; }
        .status-cancelada { background-color: #ef4444; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Nota Fiscal de Serviços Eletrônica (NFS-e)</h1>
        <p>Número: <strong>{{ str_pad($nfse->numero, 8, '0', STR_PAD_LEFT) }}</strong> | Série: <strong>{{ $nfse->serie }}</strong> | Emissão: <strong>{{ $nfse->data_emissao->format('d/m/Y') }}</strong></p>
        <p>
            Status: 
            @if($nfse->status === 'emitida')
                <span class="status-badge">EMITIDA</span>
            @elseif($nfse->status === 'cancelada')
                <span class="status-badge status-cancelada">CANCELADA</span>
            @else
                <span class="status-badge" style="background-color: #f59e0b;">RASCUNHO</span>
            @endif
        </p>
    </div>

    <div class="box">
        <div class="box-title">PRESTADOR DE SERVIÇOS</div>
        <table>
            <tr><th>Razão Social / Nome:</th><td>{{ $branch->company_name ?? 'Locadora de Veículos (Filial Padrão)' }}</td></tr>
            <tr><th>CNPJ / CPF:</th><td>{{ $branch->cnpj ?? '00.000.000/0001-00' }}</td></tr>
            <tr><th>Endereço:</th><td>{{ $branch->address ?? 'Endereço não cadastrado' }}</td></tr>
            <tr><th>Telefone:</th><td>{{ $branch->phone ?? '(00) 0000-0000' }}</td></tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">TOMADOR DE SERVIÇOS</div>
        <table>
            <tr><th>Razão Social / Nome:</th><td>{{ $nfse->tomador_nome }}</td></tr>
            <tr><th>CNPJ / CPF:</th><td>{{ $nfse->tomador_cnpj_cpf }}</td></tr>
            <tr><th>Endereço:</th><td>{{ $nfse->tomador_endereco ?? 'Não informado' }}</td></tr>
            <tr><th>E-mail:</th><td>{{ $nfse->tomador_email ?? 'Não informado' }}</td></tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">DISCRIMINAÇÃO DOS SERVIÇOS</div>
        <div style="padding: 10px; background-color: #f9f9f9; border: 1px solid #eee; min-height: 80px; font-size: 12px; white-space: pre-wrap;">{{ $nfse->discriminacao }}</div>
        
        @if($nfse->observacoes)
        <div style="margin-top: 10px;">
            <strong>Observações:</strong><br>
            <span style="font-size: 11px;">{{ $nfse->observacoes }}</span>
        </div>
        @endif
    </div>

    <div class="box">
        <div class="box-title">VALORES E IMPOSTOS</div>
        <table class="values-table">
            <tr>
                <th style="text-align: left;">CNAE / Código do Serviço</th>
                <th style="text-align: left;">{{ $nfse->codigo_servico ?? '7020 - Locação de Bens Móveis' }}</th>
                <th>Valor do Serviço:</th>
                <td style="font-size: 14px;">R$ {{ number_format($nfse->valor_servico, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none;"></td>
                <th>Alíquota ISS (%):</th>
                <td>{{ number_format($nfse->aliquota_iss, 2, ',', '.') }}%</td>
            </tr>
            <tr>
                <td colspan="2" style="border: none;"></td>
                <th>Valor ISS:</th>
                <td>R$ {{ number_format($nfse->valor_iss, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Documento auxiliar da Nota Fiscal de Serviços Eletrônica.<br>
        A autenticidade deste documento pode ser conferida no site da Prefeitura Municipal.</p>
        <p>Gerado pelo Sistema Locadora 2026 em {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

</body>
</html>
