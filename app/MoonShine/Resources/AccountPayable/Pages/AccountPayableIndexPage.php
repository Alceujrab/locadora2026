<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\AccountPayable\Pages;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\SupplierResource;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\AccountPayable\AccountPayableResource;
use MoonShine\Support\ListOf;
use Throwable;
/**
 * @extends IndexPage<AccountPayableResource>
 */
class AccountPayableIndexPage extends IndexPage
{
    protected bool $isLazy = true;
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class),
            Text::make('Descrição', 'description'),
            Date::make('Vencimento', 'due_date')->format('d/m/Y')->sortable(),
            Number::make('Valor R$', 'amount')
                ->sortable()
                ->badge('red'),
            Select::make('Status', 'status')->options([
                'pendente' => 'Pendente',
                'pago' => 'Pago',
                'cancelado' => 'Cancelado'
            ])->badge(fn($status, $field) => match($status) {
                'pago' => 'success',
                'cancelado' => 'gray',
                default => 'warning'
            }),
        ];
    }
    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons();
    }
    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Fornecedor', 'supplier', resource: SupplierResource::class)->nullable(),
            Select::make('Status', 'status')->options([
                'pendente' => 'Pendente',
                'pago' => 'Pago',
                'cancelado' => 'Cancelado'
            ])->nullable(),
        ];
    }
    /**
     * @return list<QueryTag>
     */
    protected function queryTags(): array
    {
        return [];
    }
    /**
     * @return list<Metric>
     */
    protected function metrics(): array
    {
        return [];
    }
    /**
     * @param  TableBuilder  $component
     *
     * @return TableBuilder
     */
    protected function modifyListComponent(ComponentContract $component): ComponentContract
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
