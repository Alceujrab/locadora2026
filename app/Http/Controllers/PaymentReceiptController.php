<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentReceiptController extends Controller
{
    public function download(Request $request, int $id)
    {
        $account = AccountReceivable::with(['customer', 'branch', 'contract', 'invoice'])->findOrFail($id);

        // Só gera recibo se houve algum pagamento
        if ((float) $account->paid_amount <= 0) {
            abort(404, 'Nenhum pagamento registrado para esta conta.');
        }

        $methodLabels = [
            'pix'            => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito'  => 'Cartão de Débito',
            'transferencia'  => 'Transferência Bancária',
            'boleto'         => 'Boleto',
            'dinheiro'       => 'Dinheiro',
            'cheque'         => 'Cheque',
            'outro'          => 'Outro',
        ];

        // Logo da empresa em base64 (mesmo padrão dos outros PDFs do sistema)
        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Dados da empresa via configurações do sistema
        $company = [
            'name'    => Setting::get('company_name',    'Elite Locadora de Veiculos'),
            'cnpj'    => Setting::get('company_cnpj',    ''),
            'phone'   => Setting::get('company_phone',   ''),
            'email'   => Setting::get('company_email',   ''),
            'address' => Setting::get('company_address', ''),
            'city'    => Setting::get('company_city',    ''),
            'state'   => Setting::get('company_state',   ''),
            'zip'     => Setting::get('company_zip',     ''),
            'footer'  => Setting::get('invoice_footer',  'Este documento não possui validade fiscal.'),
        ];

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'account'      => $account,
            'methodLabels' => $methodLabels,
            'logoBase64'   => $logoBase64,
            'company'      => $company,
        ])->setPaper('a4', 'portrait');

        $filename = 'recibo_pagamento_' . $account->id . '.pdf';

        return $pdf->download($filename);
    }
}
