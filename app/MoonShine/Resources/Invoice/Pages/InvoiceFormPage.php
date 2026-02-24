<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice\Pages;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
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
use MoonShine\UI\Components\Layout\Box;
use Throwable;
/**
 * @extends FormPage<InvoiceResource>
 */
class InvoiceFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Informações Gerais da Fatura', [
                ID::make(),
                Text::make('Nº Fatura', 'invoice_number')->required(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class)->required(),
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)->required()->searchable(),
                BelongsTo::make('Contrato Vinculado', 'contract', resource: ContractResource::class)->nullable()->searchable(),
                Enum::make('Status', 'status')->attach(InvoiceStatus::class)->required(),
                Date::make('Vencimento', 'due_date')->required(),
                Text::make('Parcela', 'installment_number')->hint('Ex: 1/3'),
            ]),
            Box::make('Valores', [
                Number::make('Valor Base (R$)', 'amount')->step(0.01)->min(0)->required(),
                Number::make('Multa (R$)', 'penalty_amount')->step(0.01)->min(0),
                Number::make('Juros (R$)', 'interest_amount')->step(0.01)->min(0),
                Number::make('Desconto (R$)', 'discount')->step(0.01)->min(0),
                Number::make('Total Final (R$)', 'total')->step(0.01)->min(0)->required(),
            ]),
            Box::make('Nota Fiscal / Integração', [
                Text::make('Nº NFS-e', 'nfse_number'),
                Textarea::make('Observações', 'notes'),
            ]),
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
