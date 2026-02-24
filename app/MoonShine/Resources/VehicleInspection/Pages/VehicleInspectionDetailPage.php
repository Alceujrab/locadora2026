<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection\Pages;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Components\Table\TableBuilder;
use MoonShine\Contracts\UI\FieldContract;
use App\MoonShine\Resources\VehicleInspection\VehicleInspectionResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Enum;
use MoonShine\UI\Fields\Date;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Textarea;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
use MoonShine\Laravel\Fields\Relationships\HasMany;
use App\MoonShine\Resources\VehicleResource;
use App\MoonShine\Resources\ContractResource;
use App\MoonShine\Resources\InspectionItem\InspectionItemResource;
use App\MoonShine\Resources\MoonShineUser\MoonShineUserResource;
use App\Enums\InspectionType;
use MoonShine\UI\Components\Layout\Box;
use Throwable;
/**
 * @extends DetailPage<VehicleInspectionResource>
 */
class VehicleInspectionDetailPage extends DetailPage
{
    /**
     * @return list<FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Informações Gerais', [
                ID::make(),
                BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class),
                BelongsTo::make('Contrato', 'contract', resource: ContractResource::class),
                Enum::make('Tipo de Vistoria', 'type')->attach(InspectionType::class),
                Date::make('Data da Vistoria', 'inspection_date')->format('d/m/Y H:i'),
                BelongsTo::make('Vistoriador', 'inspector', resource: MoonShineUserResource::class),
            ]),
            Box::make('Condições do Veículo', [
                Number::make('Quilometragem', 'mileage'),
                Text::make('Nível de Combustível', 'fuel_level'),
                Text::make('Condição Geral', 'overall_condition'),
                Textarea::make('Observações', 'notes'),
            ]),
            HasMany::make('Itens Inspecionados', 'items', resource: InspectionItemResource::class),
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
