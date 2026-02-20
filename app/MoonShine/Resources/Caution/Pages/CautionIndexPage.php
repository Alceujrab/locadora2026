<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Caution\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Date;
use App\MoonShine\Resources\Caution\CautionResource;
use MoonShine\Support\ListOf;
use Throwable;


/**
 * @extends IndexPage<CautionResource>
 */
class CautionIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Contrato', 'contract', 'id', \App\MoonShine\Resources\ContractResource::class)
                ->badge('purple'),
            BelongsTo::make('Cliente', 'customer', 'name', \App\MoonShine\Resources\CustomerResource::class),
            Text::make('Tipo', 'type')
                ->badge(fn($type) => $type === 'cartao' ? 'info' : 'warning'),
            Number::make('Valor', 'amount')
                ->sortable(),
            Text::make('Status', 'status')
                ->badge(fn($status) => match($status) {
                    'retida' => 'warning',
                    'liberada' => 'success',
                    'cobrada_parcial' => 'info',
                    'cobrada_total' => 'error',
                    default => 'gray'
                }),
            Date::make('Liberado em', 'released_at')
                ->format('d/m/Y H:i')
                ->sortable(),
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
        return [];
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
