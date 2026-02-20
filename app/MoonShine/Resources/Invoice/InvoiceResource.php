<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice;
use App\MoonShine\Resources\Invoice\Pages\InvoiceIndexPage;
use App\MoonShine\Resources\Invoice\Pages\InvoiceFormPage;
use App\MoonShine\Resources\Invoice\Pages\InvoiceDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
use App\Services\MercadoPagoService;
use App\Models\Payment;
use App\Enums\PaymentMethod;

/**
 * @extends ModelResource<Invoice, InvoiceIndexPage, InvoiceFormPage, InvoiceDetailPage>
 */
class InvoiceResource extends ModelResource
{
    protected string $model = Invoice::class;

    protected string $title = 'Invoices';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            InvoiceIndexPage::class,
            InvoiceFormPage::class,
            InvoiceDetailPage::class,
        ];
    }

    public function query(): \Illuminate\Contracts\Database\Eloquent\Builder
    {
        $query = parent::query();
        return $query;
    }

    public function getActiveActions(): array
    {
        return parent::getActiveActions();
    }

    public function generatePix(Invoice $invoice, MercadoPagoService $mpService)
    {
        if ($invoice->status === \App\Enums\InvoiceStatus::PAID) {
            return MoonShineJsonResponse::make()->toast('Fatura já está paga.', 'error');
        }

        // Criar um pagamento local pendente
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total_with_charges ?? $invoice->total,
            'method' => PaymentMethod::PIX,
            'mp_status' => 'pending'
        ]);

        $pixData = $mpService->generatePix($payment, $invoice);

        if ($pixData) {
            return MoonShineJsonResponse::make()
                ->toast('PIX gerado com sucesso!', 'success')
                ->redirect(route('moonshine.resource.invoice-resource.detail', $invoice->id));
        }

        // Caso falhe, deleta o registro pendente
        $payment->delete();
        
        return MoonShineJsonResponse::make()
            ->toast('Falha ao gerar PIX com Mercado Pago.', 'error');
    }
}
