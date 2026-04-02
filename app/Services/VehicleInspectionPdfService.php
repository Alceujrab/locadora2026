<?php

namespace App\Services;

use App\Models\VehicleInspection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class VehicleInspectionPdfService
{
    public function generatePdf(VehicleInspection $inspection): string
    {
        $inspection->loadMissing([
            'vehicle',
            'contract.customer',
            'contract.branch',
            'inspector',
            'items',
        ]);

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $signatureBase64 = null;
        if ($inspection->signature_image) {
            $signatureContent = $this->readFileFromKnownDisks($inspection->signature_image);
            if ($signatureContent !== null) {
                $signatureBase64 = 'data:image/png;base64,' . base64_encode($signatureContent);
            }
        }

        $itemPhotos = [];
        foreach ($inspection->items as $item) {
            $itemPhotos[$item->id] = collect($item->photos ?? [])
                ->filter(fn ($path) => is_string($path) && $path !== '')
                ->map(function (string $path) {
                    $content = $this->readFileFromKnownDisks($path);
                    if ($content === null) {
                        return null;
                    }

                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    $mimeType = match ($extension) {
                        'png' => 'image/png',
                        'gif' => 'image/gif',
                        'webp' => 'image/webp',
                        default => 'image/jpeg',
                    };

                    return [
                        'path' => $path,
                        'base64' => 'data:' . $mimeType . ';base64,' . base64_encode($content),
                    ];
                })
                ->filter()
                ->values()
                ->all();
        }

        $pdf = Pdf::loadView('pdf.vehicle-inspection-pdf', [
            'inspection' => $inspection,
            'logoBase64' => $logoBase64,
            'signatureBase64' => $signatureBase64,
            'itemPhotos' => $itemPhotos,
        ]);

        $fileName = 'inspection-pdfs/vistoria-' . $inspection->id . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($fileName, $pdf->output());

        $inspection->update(['pdf_path' => $fileName]);

        return $fileName;
    }

    private function readFileFromKnownDisks(string $path): ?string
    {
        foreach (['public', 'local'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return Storage::disk($disk)->get($path);
            }
        }

        return null;
    }
}