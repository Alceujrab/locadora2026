<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract;
use App\Enums\ContractStatus;
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
        if (!$contract->pdf_path || !Storage::disk('public')->exists($contract->pdf_path)) {
            abort(404, 'O PDF do contrato não foi gerado ou não está disponível.');
        }

        // Gera um token de sessão para a assinatura se não existir
        if (!$contract->signature_token) {
            $contract->update(['signature_token' => Str::random(40)]);
        }

        return view('public.contract.signature', compact('contract'));
    }

    /**
     * Process the signature submission.
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
        ]);

        if ($request->signature_token !== $contract->signature_token) {
            return back()->with('error', 'Token de assinatura inválido ou expirado.');
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $hashData = $contract->id . $contract->customer_id . now()->toIso8601String() . $ip . $userAgent;
        $hash = hash('sha256', $hashData);

        $contract->update([
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => $hash,
            'signature_method' => 'Aceite Digital Web (Token)',
            'status' => $contract->status === ContractStatus::AWAITING_SIGNATURE ? ContractStatus::ACTIVE : $contract->status,
        ]);

        return redirect()->route('contract.signature.show', $id)
                         ->with('success', 'Contrato assinado digitalmente com sucesso!');
    }
}
