<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MaintenanceAlert\Pages;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\MaintenanceAlert\MaintenanceAlertResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use Throwable;
/**
 * @extends FormPage<MaintenanceAlertResource>
 */
class MaintenanceAlertFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make('Definição do Alerta', [
                ID::make(),
                \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Veículo', 'vehicle', resource: \App\MoonShine\Resources\VehicleResource::class)
                    ->required()
                    ->searchable(),
                \MoonShine\UI\Fields\Text::make('Tipo de Manutenção', 'type')
                    ->hint('Ex: Troca de é“leo, Alinhamento, Correia Dentada')
                    ->required(),
                \MoonShine\UI\Fields\Textarea::make('Descrição', 'description'),
            ]),
            Box::make('Regras de Gatilho (Quando Avisar)', [
                \MoonShine\UI\Fields\Number::make('Avisar a cada (KM)', 'trigger_km')
                    ->min(1)
                    ->hint('Ex: 10000 para avisar a cada 10 mil KM rodados')
                    ->required(),
                \MoonShine\UI\Fields\Number::make('Ou avisar a cada (Dias)', 'trigger_days')
                    ->min(1)
                    ->hint('Ex: 180 para avisar a cada 6 meses (Opcional)')
                    ->nullable(),
            ]),
            Box::make('éšltima Revisão Realizada', [
                \MoonShine\UI\Fields\Date::make('Data da Revisão', 'last_service_date')
                    ->nullable(),
                \MoonShine\UI\Fields\Number::make('Hodômetro na Revisão (KM)', 'last_service_km')
                    ->min(0)
                    ->nullable(),
            ]),
            Box::make('Configurações', [
                \MoonShine\UI\Fields\Switcher::make('Alerta Ativado', 'is_active')
                    ->default(true),
            ])
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
