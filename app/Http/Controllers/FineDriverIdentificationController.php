<?php

namespace App\Http\Controllers;

use App\Models\FineTraffic;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;

class FineDriverIdentificationController extends Controller
{
    /**
     * Gera o FICI — Formulário de Identificação do Condutor Infrator
     * no padrão CONTRAN Res. 918/2022 (substituta da 404/2012).
     */
    public function download(int $id)
    {
        $fine = FineTraffic::with(['vehicle', 'customer', 'contract.customer'])->findOrFail($id);

        if (empty($fine->driver_name) || empty($fine->driver_cpf)) {
            abort(422, 'Preencha os dados do condutor informado antes de gerar o FICI.');
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $company = [
            'name'    => Setting::get('company_name',    'Elite Locadora de Veículos'),
            'cnpj'    => Setting::get('company_cnpj',    ''),
            'phone'   => Setting::get('company_phone',   ''),
            'email'   => Setting::get('company_email',   ''),
            'address' => Setting::get('company_address', ''),
            'city'    => Setting::get('company_city',    ''),
            'state'   => Setting::get('company_state',   ''),
            'zip'     => Setting::get('company_zip',     ''),
        ];

        $pdf = Pdf::loadView('pdf.fine-driver-identification', [
            'fine'       => $fine,
            'company'    => $company,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'portrait');

        $plate = $fine->vehicle?->plate ?? 'VEICULO';
        $ait   = $fine->auto_infraction_number ?: $fine->id;
        $filename = "FICI_{$plate}_{$ait}.pdf";

        return $pdf->download($filename);
    }
}
