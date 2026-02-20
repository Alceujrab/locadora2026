<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MaintenanceAlert\Pages;

use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use App\MoonShine\Resources\MaintenanceAlert\MaintenanceAlertResource;
use MoonShine\Support\ListOf;
use Throwable;


/**
 * @extends IndexPage<MaintenanceAlertResource>
 */
class MaintenanceAlertIndexPage extends IndexPage
{
    protected bool $isLazy = true;

    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Veículo', 'vehicle', resource: \App\MoonShine\Resources\VehicleResource::class),
            \MoonShine\UI\Fields\Text::make('Tipo', 'type'),
            \MoonShine\UI\Fields\Number::make('Gatilho (KM)', 'trigger_km')->badge('purple'),
            \MoonShine\UI\Fields\Date::make('Última Revisão', 'last_service_date')->format('d/m/Y'),
            \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active'),
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
