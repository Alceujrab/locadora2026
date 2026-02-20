<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Payment\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
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
use Throwable;


/**
 * @extends FormPage<PaymentResource>
 */
class PaymentFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Dados do Pagamento', [
                ID::make(),
                BelongsTo::make('Fatura Referente', 'invoice', resource: InvoiceResource::class)->required()->searchable(),
                Enum::make('Método de Pagamento', 'method')->attach(PaymentMethod::class)->required(),
                Number::make('Valor Pago (R$)', 'amount')->step(0.01)->min(0)->required(),
                Date::make('Data e Hora do Pagamento', 'paid_at')->withTime()->required(),
            ]),

            Box::make('Integrações (Mercado Pago / Outros)', [
                Text::make('ID MP (Gateway)', 'mp_payment_id'),
                Text::make('Status MP', 'mp_status'),
                Text::make('ID Transação Própria', 'transaction_id'),
            ]),

            Box::make('Reembolsos & Notas', [
                Date::make('Data do Reembolso', 'refunded_at')->withTime(),
                Number::make('Valor Reembolsado', 'refund_amount')->step(0.01)->min(0),
                Textarea::make('Anotações Internas', 'notes'),
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

    protected function formButtons(): ListOf
    {
        return parent::formButtons();
    }

    protected function rules(DataWrapperContract $item): array
    {
        return [];
    }

    /**
     * @param  FormBuilder  $component
     *
     * @return FormBuilder
     */
    protected function modifyFormComponent(FormBuilderContract $component): FormBuilderContract
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
