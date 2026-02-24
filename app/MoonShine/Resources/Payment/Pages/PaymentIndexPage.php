<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Payment\Pages;
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
use App\MoonShine\Resources\Invoice\InvoiceResource;
use App\Enums\PaymentMethod;
use App\MoonShine\Resources\Payment\PaymentResource;
use MoonShine\Support\ListOf;
use Throwable;
/**
 * @extends IndexPage<PaymentResource>
 */
class PaymentIndexPage extends IndexPage
{
    protected bool $isLazy = true;
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('Fatura', 'invoice', resource: InvoiceResource::class),
            Enum::make('Mé©todo', 'method')->attach(PaymentMethod::class)->sortable(),
            Number::make('Valor R$', 'amount')->sortable()->badge('green'),
            Date::make('Data Pagamento', 'paid_at')->format('d/m/Y H:i')->sortable(),
            Text::make('Gateway (MP)', 'mp_payment_id')->badge('gray'),
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
            BelongsTo::make('Fatura', 'invoice', resource: InvoiceResource::class)->nullable(),
            Enum::make('Mé©todo', 'method')->attach(PaymentMethod::class)->nullable(),
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
