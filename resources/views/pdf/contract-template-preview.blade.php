<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Preview - {{ $template->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px 30px; line-height: 1.6; }
        .header { border-bottom: 3px solid #1a1a2e; padding-bottom: 10px; margin-bottom: 20px; }
        .header-table { width: 100%; border: none; }
        .header-table td { vertical-align: middle; border: none; padding: 0; }
        .preview-badge { background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 4px; font-size: 10px; font-weight: bold; display: inline-block; margin-bottom: 10px; }
        .content { padding: 0 10px; }
        .content h2 { font-size: 16px; color: #1a1a2e; border-bottom: 1px solid #ddd; padding-bottom: 4px; margin-top: 20px; }
        .content h3 { font-size: 14px; color: #374151; margin-top: 16px; }
        .content p { margin: 6px 0; text-align: justify; }
        .content ul, .content ol { margin-left: 20px; }
        .content li { margin-bottom: 4px; }
        .content blockquote { border-left: 3px solid #1a1a2e; padding-left: 10px; margin: 10px 0; color: #555; font-style: italic; }
        .footer { margin-top: 40px; border-top: 1px solid #ddd; padding-top: 10px; font-size: 9px; color: #999; text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70px;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" style="max-width: 60px; max-height: 60px;" alt="Logo">
                    @endif
                </td>
                <td>
                    <span class="preview-badge">⚠ PREVIEW - DADOS DE EXEMPLO</span>
                    <h1 style="font-size: 18px; color: #1a1a2e; margin: 4px 0;">{{ $template->name }}</h1>
                    <p style="font-size: 10px; color: #888; margin: 0;">Template: {{ $template->name }} | Filial: {{ $template->branch?->name ?? '-' }}</p>
                </td>
                <td style="text-align: right; width: 180px; font-size: 9px; color: #777;">
                    <p>{{ \App\Models\Setting::get('company_name', 'Elite Locadora') }}</p>
                    <p>CNPJ: {{ \App\Models\Setting::get('company_cnpj', '00.000.000/0001-00') }}</p>
                    <p>Tel: {{ \App\Models\Setting::get('company_phone', '(66) 3521-0000') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="content">
        {!! $content !!}
    </div>

    <div class="footer">
        <p>PREVIEW gerado em {{ now()->format('d/m/Y H:i:s') }} | Template: {{ $template->name }} | Os dados acima sao fictícios</p>
    </div>

</body>
</html>
