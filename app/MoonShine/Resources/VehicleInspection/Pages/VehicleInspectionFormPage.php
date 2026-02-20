<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection\Pages;

use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
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
 * @extends FormPage<VehicleInspectionResource>
 */
class VehicleInspectionFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Informações Gerais', [
                ID::make(),
                BelongsTo::make('Veículo', 'vehicle', resource: VehicleResource::class)->required()->searchable(),
                BelongsTo::make('Contrato', 'contract', resource: ContractResource::class)->nullable()->searchable(),
                Enum::make('Tipo de Vistoria', 'type')->attach(InspectionType::class)->required(),
                Date::make('Data da Vistoria', 'inspection_date')->withTime()->required(),
                BelongsTo::make('Vistoriador', 'inspector', resource: MoonShineUserResource::class)->required()->searchable(),
            ]),

            Box::make('Condições do Veículo', [
                Number::make('Quilometragem', 'mileage')->required()->min(0),
                Text::make('Nível de Combustível', 'fuel_level')->required()
                    ->hint('Ex: 1/4, 1/2, 3/4, Cheio, Reserva'),
                Text::make('Condição Geral', 'overall_condition')->required()
                    ->hint('Ex: Excelente, Bom, Regular, Ruim'),
                Textarea::make('Observações', 'notes'),
            ]),

            HasMany::make('Itens Inspecionados', 'items', resource: InspectionItemResource::class)->creatable(),
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
