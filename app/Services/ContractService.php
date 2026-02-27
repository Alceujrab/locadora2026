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
        // Garantir que os relacionamentos estejam carregados
        $contract->loadMissing(['template', 'customer', 'vehicle', 'branch']);

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
     * Formato das variáveis: {{contrato_numero}}, {{cliente_nome}}, {{veiculo_placa}}, etc.
     */
    protected function replaceVariables(string $html, Contract $contract): string
    {
        $contract->loadMissing(['customer', 'vehicle', 'branch']);

        $customer = $contract->customer;
        $vehicle = $contract->vehicle;
        $branch = $contract->branch;

        $setting = fn (string $key, string $default = '') => \App\Models\Setting::get($key, $default);

        $variables = [
            // ── Contrato ─────────────────────────────────────────────
            '{{contrato_numero}}'          => $contract->contract_number ?? '',
            '{{contrato_inicio}}'          => $contract->pickup_date?->format('d/m/Y') ?? '',
            '{{contrato_fim}}'             => $contract->return_date?->format('d/m/Y') ?? '',
            '{{contrato_valor_mensal}}'    => 'R$ ' . number_format((float) $contract->daily_rate, 2, ',', '.'),
            '{{contrato_valor_total}}'     => 'R$ ' . number_format((float) $contract->total, 2, ',', '.'),
            '{{contrato_caucao}}'          => 'R$ ' . number_format((float) $contract->caution_amount, 2, ',', '.'),
            '{{contrato_km_livre}}'        => '',
            '{{contrato_km_excedente}}'    => '',
            '{{contrato_data_assinatura}}' => $contract->signed_at?->format('d/m/Y') ?? now()->format('d/m/Y'),

            // ── Cliente ──────────────────────────────────────────────
            '{{cliente_nome}}'     => $customer?->name ?? '',
            '{{cliente_cpf}}'      => $customer?->cpf_cnpj ?? '',
            '{{cliente_cnpj}}'     => $customer?->cpf_cnpj ?? '',
            '{{cliente_rg}}'       => $customer?->rg ?? '',
            '{{cliente_email}}'    => $customer?->email ?? '',
            '{{cliente_telefone}}' => $customer?->phone ?? '',
            '{{cliente_endereco}}' => $customer
                ? trim(implode(', ', array_filter([
                    $customer->address_street,
                    $customer->address_number,
                    $customer->address_complement,
                    $customer->address_neighborhood,
                ])))
                : '',
            '{{cliente_cidade}}'   => $customer?->address_city ?? '',
            '{{cliente_estado}}'   => $customer?->address_state ?? '',
            '{{cliente_cep}}'      => $customer?->address_zip ?? '',
            '{{cliente_cnh}}'      => $customer?->cnh_number ?? '',

            // ── Veículo ──────────────────────────────────────────────
            '{{veiculo_placa}}'       => $vehicle?->plate ?? '',
            '{{veiculo_marca}}'       => $vehicle?->brand ?? '',
            '{{veiculo_modelo}}'      => $vehicle?->model ?? '',
            '{{veiculo_ano}}'         => $vehicle ? "{$vehicle->year_manufacture}/{$vehicle->year_model}" : '',
            '{{veiculo_cor}}'         => $vehicle?->color ?? '',
            '{{veiculo_chassi}}'      => $vehicle?->chassis ?? '',
            '{{veiculo_renavam}}'     => $vehicle?->renavam ?? '',
            '{{veiculo_km}}'          => $vehicle?->mileage ? number_format((int) $vehicle->mileage, 0, ',', '.') . ' km' : '',
            '{{veiculo_combustivel}}' => $vehicle?->fuel ?? '',

            // ── Empresa (Settings) ───────────────────────────────────
            '{{empresa_nome}}'     => $setting('company_name', 'Elite Locadora de Veículos'),
            '{{empresa_cnpj}}'     => $setting('company_cnpj', ''),
            '{{empresa_telefone}}' => $setting('company_phone', ''),
            '{{empresa_email}}'    => $setting('company_email', ''),
            '{{empresa_endereco}}' => $setting('company_address', ''),
            '{{empresa_cidade}}'   => $setting('company_city', ''),
            '{{empresa_estado}}'   => $setting('company_state', ''),

            // ── Datas e Outros ───────────────────────────────────────
            '{{data_atual}}'            => now()->format('d/m/Y'),
            '{{data_extenso}}'          => now()->translatedFormat('d \\d\\e F \\d\\e Y'),
            '{{filial_nome}}'           => $branch?->name ?? '',
            '{{filial_endereco}}'       => $branch?->full_address ?? '',
            '{{assinatura_locador}}'    => '___________________________________',
            '{{assinatura_locatario}}'  => '___________________________________',
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
