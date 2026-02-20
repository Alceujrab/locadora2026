<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Payment\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Payment\PaymentResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\Enums\PaymentMethod;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\Preview;
use Throwable;/**
 * @extends DetailPage<PaymentResource>
 */
class PaymentDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Dados do Pagamento', [
                ID::make(),
                BelongsTo::make('Fatura Recebida', 'invoice', resource: InvoiceResource::class),
                Enum::make('Método de Pagamento', 'method')->attach(PaymentMethod::class),
                Number::make('Valor Pago (R$)', 'amount'),
                Date::make('Data e Hora', 'paid_at')->format('d/m/Y H:i'),
            ]),

            Box::make('Integrações (Mercado Pago / Outros)', [
                Text::make('ID MP (Gateway)', 'mp_payment_id'),
                Text::make('Status MP', 'mp_status'),
                Text::make('ID Transação', 'transaction_id'),
            ]),

            Box::make('Reembolsos & Notas', [
                Date::make('Data do Reembolso', 'refunded_at')->format('d/m/Y H:i'),
                Number::make('Valor Reembolsado', 'refund_amount'),
                Textarea::make('Anotações', 'notes'),
            ]),

            Box::make('PIX Cobrança', [
                Preview::make('QR Code', 'pix_qr_code_base64', function($item) {
                    if (!$item->pix_qr_code_base64) return '';
                    $src = str_starts_with($item->pix_qr_code_base64, 'data:image') 
                        ? $item->pix_qr_code_base64 
                        : 'data:image/jpeg;base64,' . $item->pix_qr_code_base64;
                    return "<img src='{$src}' style='max-width: 250px; border-radius: 8px;' />";
                }),
                Textarea::make('Copia e Cola', 'pix_qr_code')->customAttributes(['readonly' => true, 'rows' => 4]),
            ])->canSee(fn($payment) => $payment->method === PaymentMethod::PIX && !empty($payment->pix_qr_code_base64)),
        ];
    }

    protected function buttons(): ListOf
    {
        return parent::buttons();
    }

    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyDetailComponent(ComponentContract $component): ComponentContract
    {
        return $component;
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function topLayer(): array
    {
        return [
            ...parent::topLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function mainLayer(): array
    {
        return [
            ...parent::mainLayer()
        ];
    }

    /**
     * @return list<ComponentContract>
     * @throws Throwable
     */
    protected function bottomLayer(): array
    {
        return [
            ...parent::bottomLayer()
        ];
    }
}
