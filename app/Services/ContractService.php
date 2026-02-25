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

        $pdf = Pdf::loadHTML($html);

        // Define papel e orientação se desejar
        $pdf->setPaper('A4', 'portrait');

        $fileName = 'contracts/'.$contract->contract_number.'_'.time().'.pdf';

        Storage::disk('public')->put($fileName, $pdf->output());

        // Atualiza o caminho e status se estiver como rascunho
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
