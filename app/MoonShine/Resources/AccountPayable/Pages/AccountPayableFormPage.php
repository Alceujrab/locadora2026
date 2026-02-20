<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountPayable\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\AccountPayable\AccountPayableResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\SupplierResource;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\BranchResource;
use MoonShine\UI\Components\Layout\Box;
use Throwable;


/**
 * @extends FormPage<AccountPayableResource>
 */
class AccountPayableFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Detalhes da Despesa', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class)->required()->searchable(),
                BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class)->searchable()->nullable(),
                BelongsTo::make('Veículo (Opcional)', 'vehicle', resource: VehicleResource::class)->searchable()->nullable(),
                
                Text::make('Categoria', 'category')->required()
                    ->hint('Ex: Manutenção, Imposto, Operacional'),
                Text::make('Descrição', 'description')->required(),
                Number::make('Valor (R$)', 'amount')->step(0.01)->min(0)->required(),
                Date::make('Data de Vencimento', 'due_date')->required(),
                
                Select::make('Status', 'status')->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado'
                ])->required(),
            ]),

            Box::make('Pagamento', [
                Date::make('Data do Pagamento', 'paid_at')->withTime(),
                Text::make('Método de Pagamento', 'payment_method')->hint('Boleto, Pix, Cartão, Dinheiro'),
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
