<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection\Pages;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Laravel\QueryTags\QueryTag;
use MoonShine\UI\Components\Metrics\Wrapped\Metric;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Date;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\ContractResource;
use App\Enums\InspectionType;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
use MoonShine\Support\ListOf;
use Throwable;
/**
 * @extends IndexPage<VehicleInspectionResource>
 */
class VehicleInspectionIndexPage extends IndexPage
{
    protected bool $isLazy = true;
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            ID::make()->sortable(),
            BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class),
            BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable(),
            Enum::make('Tipo', 'type')->attach(InspectionType::class),
            Date::make('Data Vistoria', 'inspection_date')->format('d/m/Y H:i')->sortable(),
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
            BelongsTo::make('VeÃ­culo', 'vehicle', resource: VehicleResource::class)->nullable(),
            Enum::make('Tipo', 'type')->attach(InspectionType::class)->nullable(),
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
