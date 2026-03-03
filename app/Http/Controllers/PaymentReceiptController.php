<?php

namespace App\Http\Controllers;

use App\Models\AccountReceivable;
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
            'pix'               => 'PIX',
            'cartao_credito'    => 'Cartão de Crédito',
            'cartao_debito'     => 'Cartão de Débito',
            'transferencia'     => 'Transferência Bancária',
            'boleto'            => 'Boleto',
            'dinheiro'          => 'Dinheiro',
            'cheque'            => 'Cheque',
            'outro'             => 'Outro',
        ];

        $data = [
            'account'      => $account,
            'methodLabels' => $methodLabels,
        ];

        $pdf = Pdf::loadView('pdf.payment-receipt', $data)
            ->setPaper('a4', 'portrait');

        $filename = 'recibo_pagamento_' . $account->id . '.pdf';

        return $pdf->download($filename);
    }
}
