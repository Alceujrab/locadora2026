<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceOrderSignatureController extends Controller
{
    /**
     * Show the service order signature page for the customer.
     */
    public function show($id)
    {
        $order = ServiceOrder::with(['customer', 'vehicle', 'branch', 'supplier', 'items', 'openedByUser'])->findOrFail($id);

        // Se já estiver assinada, mostra página de sucesso
        if ($order->isSigned()) {
            return view('public.service-order.signed_success', compact('order'));
        }

        // Se não tem PDF gerado, não pode assinar
        if (! $order->pdf_path || ! Storage::disk('public')->exists($order->pdf_path)) {
            abort(404, 'O PDF da OS não foi gerado ou não está disponível.');
        }

        return view('public.service-order.signature', compact('order'));
    }

    /**
     * Process the signature submission.
     */
    public function sign(Request $request, $id)
    {
        $order = ServiceOrder::findOrFail($id);

        if ($order->isSigned()) {
            return redirect()->route('os.signature.show', $id)
                ->with('error', 'Esta OS já foi assinada.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
        ]);

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $hashData = $order->id . ($order->customer_id ?? '') . now()->toIso8601String() . $ip . $userAgent;
        $hash = hash('sha256', $hashData);

        $order->update([
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => $hash,
            'status' => ServiceOrderStatus::COMPLETED,
        ]);

        return redirect()->route('os.signature.show', $id)
            ->with('success', 'Ordem de Serviço assinada digitalmente com sucesso!');
    }

    /**
     * Download the OS PDF.
     */
    public function downloadPdf($id)
    {
        $order = ServiceOrder::findOrFail($id);

        if (! $order->pdf_path || ! Storage::disk('public')->exists($order->pdf_path)) {
            abort(404, 'PDF não disponível.');
        }

        return Storage::disk('public')->download($order->pdf_path, 'OS-' . $order->id . '.pdf');
    }
}
