<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice\Pages;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\BranchResource;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\MercadoPagoService;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use MoonShine\UI\Components\ActionButton;
use MoonShine\Laravel\Http\Responses\MoonShineJsonResponse;
/**
 * @extends DetailPage<InvoiceResource>
 */
class InvoiceDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('InformaÃ§Ãµes Gerais da Fatura', [
                ID::make(),
                Text::make('NÂº Fatura', 'invoice_number'),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
                BelongsTo::make('Contrato Vinculado', 'contract', resource: ContractResource::class),
                Enum::make('Status', 'status')->attach(InvoiceStatus::class),
                Date::make('Vencimento', 'due_date')->format('d/m/Y'),
                Text::make('Parcela', 'installment_number'),
            ]),
            Box::make('Valores', [
                Number::make('Valor Base (R$)', 'amount'),
                Number::make('Multa (R$)', 'penalty_amount'),
                Number::make('Juros (R$)', 'interest_amount'),
                Number::make('Desconto (R$)', 'discount'),
                Number::make('Total Final (R$)', 'total'),
            ]),
            Box::make('Nota Fiscal / IntegraÃ§Ã£o', [
                Text::make('NÂº NFS-e', 'nfse_number'),
                Textarea::make('ObservaÃ§Ãµes', 'notes'),
            ]),
            HasMany::make('Pagamentos Relacionados', 'payments', resource: \App\MoonShine\Resources\Payment\PaymentResource::class),
        ];
    }
    protected function buttons(): ListOf
    {
        return parent::buttons()->add(
            ActionButton::make('Gerar PIX', '#')
                ->method('generatePix')
                ->icon('qr-code')
                ->success()
                ->canSee(fn($invoice) => $invoice->status === InvoiceStatus::OPEN || $invoice->status === InvoiceStatus::OVERDUE)
        );
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
