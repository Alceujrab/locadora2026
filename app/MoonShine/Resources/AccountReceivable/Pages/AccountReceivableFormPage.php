<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountReceivable\Pages;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\AccountReceivable\AccountReceivableResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\MoonShine\Resources\BranchResource;
use MoonShine\UI\Components\Layout\Box;
use Throwable;
/**
 * @extends FormPage<AccountReceivableResource>
 */
class AccountReceivableFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Detalhes do Recebimento', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class)->required()->searchable(),
                BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)->searchable()->nullable(),
                BelongsTo::make('Contrato (Opcional)', 'contract', resource: ContractResource::class)->searchable()->nullable(),
                BelongsTo::make('Fatura (Opcional)', 'invoice', resource: InvoiceResource::class)->searchable()->nullable(),
                Text::make('Categoria', 'category')->required()
                    ->hint('Ex: Locação, Multa, Indenização'),
                Text::make('Descrição', 'description')->required(),
                Number::make('Valor (R$)', 'amount')->step(0.01)->min(0)->required(),
                Date::make('Data de Vencimento', 'due_date')->required(),
                Select::make('Status', 'status')->options([
                    'pendente' => 'Pendente',
                    'recebido' => 'Recebido',
                    'cancelado' => 'Cancelado'
                ])->required(),
            ]),
            Box::make('Pagamento', [
                Date::make('Data do Recebimento', 'received_at')->withTime(),
                Text::make('Método de Pagamento', 'payment_method')->hint('Pix, Cartão de Crédito, Boleto'),
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
