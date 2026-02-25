<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SignatureController extends Controller
{
    /**
     * Show the contract signature page for the customer.
     */
    public function show($id)
    {
        $contract = Contract::with(['customer', 'vehicle', 'branch'])->findOrFail($id);

        // Se já estiver assinado, mostra página de sucesso
        if ($contract->isSigned()) {
            return view('public.contract.signed_success', compact('contract'));
        }

        // Se não tem PDF gerado, não pode assinar
        if (! $contract->pdf_path || ! Storage::disk('public')->exists($contract->pdf_path)) {
            abort(404, 'O PDF do contrato não foi gerado ou não está disponível.');
        }

        // Gera um token de sessão para a assinatura se não existir
        if (! $contract->signature_token) {
            $contract->update(['signature_token' => Str::random(40)]);
        }

        return view('public.contract.signature', compact('contract'));
    }

    /**
     * Process the signature submission with signature image + geolocation.
     */
    public function sign(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        if ($contract->isSigned()) {
            return redirect()->route('contract.signature.show', $id)
                ->with('error', 'Este contrato já foi assinado.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
            'signature_token' => 'required|string',
            'signature_data' => 'required|string',
        ]);

        if ($request->signature_token !== $contract->signature_token) {
            return back()->with('error', 'Token de assinatura inválido ou expirado.');
        }

        // Salvar imagem da assinatura
        $signaturePath = $this->saveSignatureImage($request->input('signature_data'), $contract->id);

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $hashData = $contract->id.$contract->customer_id.now()->toIso8601String().$ip.$userAgent;
        $hash = hash('sha256', $hashData);

        $contract->update([
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => $hash,
            'signature_method' => 'Digital Web (Manuscrita+Geo)',
            'signature_image' => $signaturePath,
            'signature_latitude' => $request->input('latitude'),
            'signature_longitude' => $request->input('longitude'),
            'status' => ContractStatus::ACTIVE,
        ]);

        // Re-gerar PDF com assinatura
        $this->regeneratePdfWithSignature($contract);

        return view('public.contract.signed_success', compact('contract'));
    }

    /**
     * Download do PDF do contrato assinado
     */
    public function downloadPdf($id)
    {
        $contract = Contract::findOrFail($id);

        if (! $contract->pdf_path || ! Storage::disk('public')->exists($contract->pdf_path)) {
            abort(404, 'PDF nao disponivel.');
        }

        return Storage::disk('public')->download($contract->pdf_path, 'Contrato-' . $contract->contract_number . '.pdf');
    }

    /**
     * Salva a imagem da assinatura como PNG
     */
    private function saveSignatureImage(string $base64Data, string $contractId): ?string
    {
        if (! str_starts_with($base64Data, 'data:image/png;base64,')) {
            return null;
        }

        $imageData = base64_decode(str_replace('data:image/png;base64,', '', $base64Data));
        $filename = "contract-signatures/contrato-{$contractId}-" . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Re-gera o PDF do contrato incluindo a assinatura
     */
    private function regeneratePdfWithSignature(Contract $contract): void
    {
        try {
            $service = app(\App\Services\ContractService::class);
            $service->generatePdf($contract);
        } catch (\Exception $e) {
            // Não falhar se o PDF não puder ser regenerado
        }
    }
}
