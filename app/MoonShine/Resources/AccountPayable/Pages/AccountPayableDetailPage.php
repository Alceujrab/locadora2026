<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountPayable\Pages;

use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
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
 * @extends DetailPage<AccountPayableResource>
 */
class AccountPayableDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Detalhes da Despesa', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class),
                BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class),
                
                Text::make('Categoria', 'category'),
                Text::make('Descrição', 'description'),
                Number::make('Valor (R$)', 'amount'),
                Date::make('Data de Vencimento', 'due_date')->format('d/m/Y'),
                
                Select::make('Status', 'status')->options([
                    'pendente' => 'Pendente',
                    'pago' => 'Pago',
                    'cancelado' => 'Cancelado'
                ])->badge(fn($status, $field) => match($status) {
                    'pago' => 'success',
                    'cancelado' => 'gray',
                    default => 'warning'
                }),
            ]),

            Box::make('Pagamento', [
                Date::make('Data do Pagamento', 'paid_at')->format('d/m/Y H:i'),
                Text::make('Método de Pagamento', 'payment_method'),
                Textarea::make('Observações', 'notes'),
            ]),
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
