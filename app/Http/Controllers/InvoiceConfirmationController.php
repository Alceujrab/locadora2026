<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InvoiceConfirmationController extends Controller
{
    /**
     * Mostra a página de confirmação da fatura
     */
    public function show($id)
    {
        $invoice = Invoice::with(['customer', 'branch', 'contract', 'items'])->findOrFail($id);

        // Buscar OS vinculada (se houver)
        $serviceOrder = ServiceOrder::where('invoice_id', $invoice->id)->with('vehicle')->first();

        return view('public.invoice.confirmation', compact('invoice', 'serviceOrder'));
    }

    /**
     * Confirma o recebimento da fatura
     */
    public function confirm(Request $request, $id)
    {
        $invoice = Invoice::with(['customer'])->findOrFail($id);

        if ($invoice->confirmed_at) {
            return view('public.invoice.confirmed', [
                'invoice' => $invoice,
                'message' => 'Esta fatura ja foi confirmada anteriormente.',
            ]);
        }

        $invoice->update([
            'confirmed_at' => now(),
            'confirmation_ip' => $request->ip(),
        ]);

        return view('public.invoice.confirmed', [
            'invoice' => $invoice,
            'message' => 'Recebimento confirmado com sucesso! Obrigado.',
        ]);
    }

    /**
     * Download do PDF da fatura
     */
    public function downloadPdf($id)
    {
        $invoice = Invoice::findOrFail($id);

        if (! $invoice->pdf_path || ! Storage::disk('public')->exists($invoice->pdf_path)) {
            abort(404, 'PDF nao disponivel.');
        }

        return Storage::disk('public')->download($invoice->pdf_path, 'Fatura-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Gera o PDF da fatura
     */
    public static function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['branch', 'customer', 'contract', 'items']);

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        // Buscar OS vinculada
        $serviceOrder = ServiceOrder::where('invoice_id', $invoice->id)->with('vehicle')->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice-pdf', [
            'invoice' => $invoice,
            'logoBase64' => $logoBase64,
            'serviceOrder' => $serviceOrder,
        ]);

        $filename = 'fatura-' . $invoice->invoice_number . '-' . now()->format('YmdHis') . '.pdf';
        $path = 'invoice-pdfs/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());
        $invoice->update(['pdf_path' => $path]);

        return $path;
    }
}
