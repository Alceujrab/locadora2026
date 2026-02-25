<?php

namespace App\Services;

use App\Models\Contract;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractService
{
    /**
     * Gera o PDF do contrato mesclando as variáveis do template.
     */
    public function generatePdf(Contract $contract): string|bool
    {
        $template = $contract->template;

        if (! $template || ! $template->content) {
            return false;
        }

        $html = $this->replaceVariables($template->content, $contract);

        // Adicionar bloco de assinatura se contrato foi assinado
        if ($contract->isSigned()) {
            $signatureBlock = '<div style="margin-top:40px; border-top:2px solid #1a1a2e; padding-top:20px;">';
            $signatureBlock .= '<h3 style="color:#1a1a2e; font-size:14px; margin-bottom:10px;">ASSINATURA DIGITAL</h3>';
            $signatureBlock .= '<table style="width:100%; font-size:11px; border:none;">';
            $signatureBlock .= '<tr><td style="border:none; padding:3px;"><strong>Assinado em:</strong> ' . $contract->signed_at?->format('d/m/Y H:i:s') . '</td>';
            $signatureBlock .= '<td style="border:none; padding:3px;"><strong>IP:</strong> ' . ($contract->signature_ip ?? '-') . '</td></tr>';
            if ($contract->signature_latitude) {
                $signatureBlock .= '<tr><td colspan="2" style="border:none; padding:3px;"><strong>Geolocalização:</strong> ' . $contract->signature_latitude . ', ' . $contract->signature_longitude . '</td></tr>';
            }
            $signatureBlock .= '<tr><td colspan="2" style="border:none; padding:3px;"><strong>Método:</strong> ' . ($contract->signature_method ?? '-') . '</td></tr>';
            $signatureBlock .= '<tr><td colspan="2" style="border:none; padding:3px; font-size:9px; word-break:break-all;"><strong>Hash SHA-256:</strong> ' . ($contract->signature_hash ?? '-') . '</td></tr>';
            $signatureBlock .= '</table>';

            // Imagem da assinatura
            if ($contract->signature_image && Storage::disk('public')->exists($contract->signature_image)) {
                $sigBase64 = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($contract->signature_image));
                $signatureBlock .= '<div style="margin-top:10px; text-align:center;">';
                $signatureBlock .= '<p style="font-size:10px; color:#555; margin-bottom:5px;">Assinatura do Locatário:</p>';
                $signatureBlock .= '<img src="' . $sigBase64 . '" style="max-width:300px; max-height:100px; border:1px solid #ddd; padding:5px; background:#fff;"/>';
                $signatureBlock .= '<p style="font-size:10px; margin-top:5px;">' . ($contract->customer?->name ?? '') . '</p>';
                $signatureBlock .= '</div>';
            }

            $signatureBlock .= '<p style="font-size:8px; color:#999; margin-top:15px; text-align:center;">Documento assinado digitalmente conforme MP 2.200-2/2001. Validação: ' . ($contract->signature_hash ?? '') . '</p>';
            $signatureBlock .= '</div>';

            $html .= $signatureBlock;
        }

        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'contracts/'.$contract->contract_number.'_'.time().'.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        $data = ['pdf_path' => $fileName];
        if ($contract->status === \App\Enums\ContractStatus::DRAFT) {
            $data['status'] = \App\Enums\ContractStatus::AWAITING_SIGNATURE;
        }

        $contract->update($data);

        return $fileName;
    }

    /**
     * Mescla as variáveis do contrato no HTML do template.
     */
    protected function replaceVariables(string $html, Contract $contract): string
    {
        $customer = $contract->customer;
        $vehicle = $contract->vehicle;

        $variables = [
            '{{ customer.name }}' => $customer?->name ?? '',
            '{{ customer.document }}' => $customer?->document ?? '',
            '{{ customer.email }}' => $customer?->email ?? '',
            '{{ customer.phone }}' => $customer?->phone ?? '',
            '{{ customer.address }}' => $customer ? "{$customer->address_street}, {$customer->address_number}, {$customer->address_city}-{$customer->address_state}" : '',

            '{{ vehicle.brand }}' => $vehicle?->brand ?? '',
            '{{ vehicle.model }}' => $vehicle?->model ?? '',
            '{{ vehicle.plate }}' => $vehicle?->plate ?? '',
            '{{ vehicle.renavam }}' => $vehicle?->renavam ?? '',
            '{{ vehicle.year }}' => $vehicle?->year_model ?? '',

            '{{ contract.number }}' => $contract->contract_number,
            '{{ contract.pickup_date }}' => $contract->pickup_date?->format('d/m/Y H:i') ?? '',
            '{{ contract.return_date }}' => $contract->return_date?->format('d/m/Y H:i') ?? '',
            '{{ contract.daily_rate }}' => number_format((float) $contract->daily_rate, 2, ',', '.'),
            '{{ contract.total_days }}' => $contract->total_days,
            '{{ contract.total }}' => number_format((float) $contract->total, 2, ',', '.'),
        ];

        return str_replace(array_keys($variables), array_values($variables), $html);
    }

    /**
     * Assina digitalmente o contrato (Simulação de assinatura local/IP)
     */
    public function signContract(Contract $contract, string $ipAddress, string $method = 'IP/Token Local'): bool
    {
        if ($contract->status !== \App\Enums\ContractStatus::AWAITING_SIGNATURE) {
            return false;
        }

        $token = Str::random(32);
        $hash = hash('sha256', $contract->id.$contract->customer_id.$token.time());

        $contract->update([
            'status' => \App\Enums\ContractStatus::ACTIVE,
            'signed_at' => now(),
            'signature_token' => $token,
            'signature_ip' => $ipAddress,
            'signature_hash' => $hash,
            'signature_method' => $method,
        ]);

        $contract->logs()->create([
            'user_id' => auth()->id() ?? 1,
            'action' => 'contract_signed',
            'description' => "Contrato assinado via {$method}. IP: {$ipAddress}",
        ]);

        return true;
    }
}
