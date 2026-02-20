<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Invoice\Pages;

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
use MoonShine\UI\Fields\Enum;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\CustomerResource;
use App\MoonShine\Resources\ContractResource;
use App\Enums\InvoiceStatus;
use App\MoonShine\Resources\Invoice\InvoiceResource;
use MoonShine\Support\ListOf;
use Throwable;


/**
 * @extends IndexPage<InvoiceResource>
 */
class InvoiceIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nº Fatura', 'invoice_number')->sortable(),
            BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable(),
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class),
            Date::make('Vencimento', 'due_date')->format('d/m/Y')->sortable(),
            Number::make('Total R$', 'total')->sortable()->badge('gray'),
            Enum::make('Status', 'status')->attach(InvoiceStatus::class)->sortable(),
        ];
    }

    /**
     * @return ListOf<ActionButtonContract>
     */
    protected function buttons(): ListOf
    {
        return parent::buttons()->add(
            \MoonShine\UI\Components\ExportButton::make('Exportar Faturas')
                ->csv()
                ->xlsx()
        );
    }

    /**
     * @return list<FieldContract>
     */
    protected function filters(): iterable
    {
        return [
            BelongsTo::make('Cliente', 'customer', resource: CustomerResource::class)->nullable(),
            Enum::make('Status', 'status')->attach(InvoiceStatus::class)->nullable(),
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
