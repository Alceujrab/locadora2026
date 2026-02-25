<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceOrderSignatureController extends Controller
{
    public function show($id)
    {
        $order = ServiceOrder::with(['customer', 'vehicle', 'branch', 'supplier', 'items', 'openedByUser'])->findOrFail($id);

        if ($order->isSigned()) {
            return view('public.service-order.signed_success', compact('order'));
        }

        return view('public.service-order.signature', compact('order'));
    }

    public function sign(Request $request, $id)
    {
        $order = ServiceOrder::findOrFail($id);

        if ($order->isSigned()) {
            return redirect()->route('os.signature.show', $id)
                ->with('error', 'Esta OS ja foi assinada.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
            'signature_data' => 'required|string',
        ]);

        // Salvar imagem da assinatura (base64 PNG -> arquivo)
        $signatureImage = $request->input('signature_data');
        $signaturePath = null;

        if ($signatureImage && str_starts_with($signatureImage, 'data:image/png;base64,')) {
            $imageData = base64_decode(str_replace('data:image/png;base64,', '', $signatureImage));
            $filename = 'so-signatures/os-' . $order->id . '-' . now()->format('YmdHis') . '.png';
            Storage::disk('public')->put($filename, $imageData);
            $signaturePath = $filename;
        }

        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $hashData = $order->id . ($order->customer_id ?? '') . now()->toIso8601String() . $ip . $userAgent;
        $hash = hash('sha256', $hashData);

        $order->update([
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => $hash,
            'signature_image' => $signaturePath,
            'status' => ServiceOrderStatus::COMPLETED,
        ]);

        // Regenerar PDF com assinatura embutida
        $this->regeneratePdfWithSignature($order);

        return redirect()->route('os.signature.show', $id)
            ->with('success', 'Ordem de Servico assinada com sucesso!');
    }

    public function downloadPdf($id)
    {
        $order = ServiceOrder::findOrFail($id);

        if (! $order->pdf_path || ! Storage::disk('public')->exists($order->pdf_path)) {
            abort(404, 'PDF nao disponivel.');
        }

        return Storage::disk('public')->download($order->pdf_path, 'OS-' . $order->id . '.pdf');
    }

    /**
     * Regenera o PDF incluindo a imagem da assinatura
     */
    private function regeneratePdfWithSignature(ServiceOrder $order): void
    {
        $order->load(['branch', 'vehicle', 'supplier', 'customer', 'items', 'openedByUser', 'createdBy']);

        $signatureImageBase64 = null;
        if ($order->signature_image && Storage::disk('public')->exists($order->signature_image)) {
            $imageContent = Storage::disk('public')->get($order->signature_image);
            $signatureImageBase64 = 'data:image/png;base64,' . base64_encode($imageContent);
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.service-order-pdf', [
            'order' => $order,
            'signatureImageBase64' => $signatureImageBase64,
            'logoBase64' => $logoBase64,
        ]);

        $filename = 'os-' . $order->id . '-signed-' . now()->format('YmdHis') . '.pdf';
        $path = 'so-pdfs/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $order->update(['pdf_path' => $path]);
    }
}
