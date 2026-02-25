<?php

namespace App\Http\Controllers;

use App\Enums\ServiceOrderStatus;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceOrderSignatureController extends Controller
{
    /**
     * Mostra a página de assinatura (autorização ou aprovação)
     */
    public function show($id)
    {
        $order = ServiceOrder::with(['customer', 'vehicle', 'branch', 'supplier', 'items', 'openedByUser'])->findOrFail($id);

        // Se já foi totalmente aprovada, mostrar sucesso
        if ($order->isApproved() && ! $order->needsApproval()) {
            return view('public.service-order.signed_success', compact('order'));
        }

        // Determinar tipo de assinatura
        $signatureType = 'authorization'; // default
        if ($order->needsApproval()) {
            $signatureType = 'completion';
        } elseif ($order->isAuthorized() && ! $order->needsApproval()) {
            // Já autorizou e não está aguardando aprovação — mostrar sucesso
            return view('public.service-order.signed_success', compact('order'));
        }

        return view('public.service-order.signature', compact('order', 'signatureType'));
    }

    /**
     * Assina autorização de abertura (1ª assinatura)
     */
    public function signAuthorization(Request $request, $id)
    {
        $order = ServiceOrder::findOrFail($id);

        if ($order->isAuthorized()) {
            return redirect()->route('os.signature.show', $id)
                ->with('error', 'Esta OS ja foi autorizada.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
            'signature_data' => 'required|string',
        ]);

        $signaturePath = $this->saveSignatureImage($request->input('signature_data'), $order->id, 'autorizacao');

        $ip = $request->ip();
        $hashData = $order->id . 'authorization' . now()->toIso8601String() . $ip;

        $order->update([
            'authorization_signed_at' => now(),
            'authorization_signature_image' => $signaturePath,
            'authorization_ip' => $ip,
            'signature_hash' => hash('sha256', $hashData),
            'status' => ServiceOrderStatus::AUTHORIZED,
        ]);

        // Gerar PDF de autorização
        $this->generatePdf($order, 'autorizacao');

        return view('public.service-order.signed_success', [
            'order' => $order,
            'message' => 'Autorizacao de abertura assinada com sucesso! A oficina sera informada para iniciar os servicos.',
        ]);
    }

    /**
     * Assina aprovação da conclusão (2ª assinatura)
     */
    public function signCompletion(Request $request, $id)
    {
        $order = ServiceOrder::findOrFail($id);

        if ($order->isApproved()) {
            return redirect()->route('os.signature.show', $id)
                ->with('error', 'Esta OS ja foi aprovada.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
            'signature_data' => 'required|string',
        ]);

        $signaturePath = $this->saveSignatureImage($request->input('signature_data'), $order->id, 'conclusao');

        $ip = $request->ip();
        $hashData = $order->id . 'completion' . now()->toIso8601String() . $ip;

        $order->update([
            'completion_signed_at' => now(),
            'completion_signature_image' => $signaturePath,
            'completion_ip' => $ip,
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => hash('sha256', $hashData),
            'status' => ServiceOrderStatus::COMPLETED,
        ]);

        // Gerar PDF de conclusão
        $this->generatePdf($order, 'conclusao');

        return view('public.service-order.signed_success', [
            'order' => $order,
            'message' => 'Servicos aprovados com sucesso! Em breve voce recebera a fatura correspondente.',
        ]);
    }

    /**
     * Download do PDF da OS
     */
    public function downloadPdf($id)
    {
        $order = ServiceOrder::findOrFail($id);

        if (! $order->pdf_path || ! Storage::disk('public')->exists($order->pdf_path)) {
            abort(404, 'PDF nao disponivel.');
        }

        return Storage::disk('public')->download($order->pdf_path, 'OS-' . $order->id . '.pdf');
    }

    /**
     * Salva a imagem da assinatura como PNG
     */
    private function saveSignatureImage(string $base64Data, int $orderId, string $type): ?string
    {
        if (! str_starts_with($base64Data, 'data:image/png;base64,')) {
            return null;
        }

        $imageData = base64_decode(str_replace('data:image/png;base64,', '', $base64Data));
        $filename = "so-signatures/os-{$orderId}-{$type}-" . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }

    /**
     * Gera o PDF da OS
     */
    private function generatePdf(ServiceOrder $order, string $stage = 'geral'): void
    {
        $order->load(['branch', 'vehicle', 'supplier', 'customer', 'items', 'openedByUser', 'createdBy']);

        $authSignatureBase64 = null;
        if ($order->authorization_signature_image && Storage::disk('public')->exists($order->authorization_signature_image)) {
            $authSignatureBase64 = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($order->authorization_signature_image));
        }

        $completionSignatureBase64 = null;
        if ($order->completion_signature_image && Storage::disk('public')->exists($order->completion_signature_image)) {
            $completionSignatureBase64 = 'data:image/png;base64,' . base64_encode(Storage::disk('public')->get($order->completion_signature_image));
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.service-order-pdf', [
            'order' => $order,
            'authSignatureBase64' => $authSignatureBase64,
            'completionSignatureBase64' => $completionSignatureBase64,
            'logoBase64' => $logoBase64,
            'stage' => $stage,
        ]);

        $filename = "os-{$order->id}-{$stage}-" . now()->format('YmdHis') . '.pdf';
        $path = 'so-pdfs/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $order->update(['pdf_path' => $path]);
    }
}
