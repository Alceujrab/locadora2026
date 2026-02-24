<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\ServiceOrderItem\Pages;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\Contracts\UI\FormBuilderContract;
use MoonShine\UI\Components\FormBuilder;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\Core\TypeCasts\DataWrapperContract;
use App\MoonShine\Resources\ServiceOrderItem\ServiceOrderItemResource;
use MoonShine\Support\ListOf;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Components\Layout\Box;
use Throwable;
/**
 * @extends FormPage<ServiceOrderItemResource>
 */
class ServiceOrderItemFormPage extends FormPage
{
    /**
     * @return list<ComponentContract|FieldContract>
     */
    protected function fields(): iterable
    {
        return [
            Box::make([
                ID::make(),
                \MoonShine\UI\Fields\Select::make('Tipo', 'type')
                    ->options([
                        'peca' => 'PeÃ§a',
                        'mao_de_obra' => 'MÃ£o de Obra',
                    ])
                    ->required(),
                \MoonShine\UI\Fields\Text::make('DescriÃ§Ã£o', 'description')
                    ->required(),
                \MoonShine\UI\Fields\Number::make('Qtd', 'quantity')
                    ->default(1)
                    ->min(1)
                    ->step(0.01)
                    ->required()
                    ->customAttributes([
                        'x-model' => 'item.quantity',
                        '@change' => 'item.total = (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)).toFixed(2)'
                    ]),
                \MoonShine\UI\Fields\Number::make('PreÃ§o UnitÃ¡rio', 'unit_price')
                    ->step(0.01)
                    ->min(0)
                    ->required()
                    ->customAttributes([
                        'x-model' => 'item.unit_price',
                        '@change' => 'item.total = (parseFloat(item.quantity || 0) * parseFloat(item.unit_price || 0)).toFixed(2)'
                    ]),
                \MoonShine\UI\Fields\Number::make('Total', 'total')
                    ->step(0.01)
                    ->customAttributes([
                        'x-model' => 'item.total',
                        'readonly' => 'readonly'
                    ]),
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
