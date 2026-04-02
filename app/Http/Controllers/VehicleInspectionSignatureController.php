<?php

namespace App\Http\Controllers;

use App\Models\VehicleInspection;
use App\Services\VehicleInspectionPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VehicleInspectionSignatureController extends Controller
{
    public function show(int $id, VehicleInspectionPdfService $pdfService)
    {
        $inspection = VehicleInspection::with([
            'vehicle',
            'contract.customer',
            'contract.branch',
            'items',
        ])->findOrFail($id);

        if ($inspection->isSigned()) {
            return view('public.inspection.signed_success', compact('inspection'));
        }

        if (! $inspection->pdf_path || ! Storage::disk('public')->exists($inspection->pdf_path)) {
            $pdfService->generatePdf($inspection);
            $inspection->refresh();
        }

        if (! $inspection->pdf_path || ! Storage::disk('public')->exists($inspection->pdf_path)) {
            abort(404, 'O PDF da vistoria nao esta disponivel.');
        }

        if (! $inspection->signature_token) {
            $inspection->update(['signature_token' => Str::random(40)]);
            $inspection->refresh();
        }

        return view('public.inspection.signature', compact('inspection'));
    }

    public function sign(Request $request, int $id, VehicleInspectionPdfService $pdfService)
    {
        $inspection = VehicleInspection::with([
            'vehicle',
            'contract.customer',
            'contract.branch',
            'items',
        ])->findOrFail($id);

        if ($inspection->isSigned()) {
            return redirect()->route('inspection.signature.show', $id)
                ->with('error', 'Esta vistoria ja foi assinada.');
        }

        $request->validate([
            'accept_terms' => 'accepted',
            'signature_token' => 'required|string',
            'signature_data' => 'required|string',
        ]);

        if ($request->input('signature_token') !== $inspection->signature_token) {
            return back()->with('error', 'Token de assinatura invalido ou expirado.');
        }

        $signaturePath = $this->saveSignatureImage($request->input('signature_data'), $inspection->id);
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? '';
        $hash = hash('sha256', $inspection->id . ($inspection->contract_id ?? '') . now()->toIso8601String() . $ip . $userAgent);

        $data = [
            'signed_at' => now(),
            'signature_ip' => $ip,
            'signature_hash' => $hash,
            'signature_image' => $signaturePath,
            'signature_latitude' => $request->input('latitude'),
            'signature_longitude' => $request->input('longitude'),
            'signature_method' => 'Web+Manuscrita',
        ];

        if ($inspection->status === 'rascunho') {
            $data['status'] = 'finalizado';
        }

        $inspection->update($data);
        $inspection->refresh();

        $pdfService->generatePdf($inspection);
        $inspection->refresh();

        return view('public.inspection.signed_success', compact('inspection'));
    }

    public function downloadPdf(int $id)
    {
        $inspection = VehicleInspection::findOrFail($id);

        if (! $inspection->pdf_path || ! Storage::disk('public')->exists($inspection->pdf_path)) {
            abort(404, 'PDF nao disponivel.');
        }

        return response()->download(
            Storage::disk('public')->path($inspection->pdf_path),
            'Vistoria-' . $inspection->id . '.pdf'
        );
    }

    private function saveSignatureImage(string $base64Data, int $inspectionId): ?string
    {
        if (! str_starts_with($base64Data, 'data:image/png;base64,')) {
            return null;
        }

        $imageData = base64_decode(str_replace('data:image/png;base64,', '', $base64Data));
        $fileName = 'inspection-signatures/vistoria-' . $inspectionId . '-' . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($fileName, $imageData);

        return $fileName;
    }
}