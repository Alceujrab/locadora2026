<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\RentalExtra;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
/**
 * @extends ModelResource<RentalExtra>
 */
class RentalExtraResource extends ModelResource
{
    protected string $model = RentalExtra::class;
    protected string $title = 'Extras de Locação';
    protected string $column = 'name';
    protected bool $columnSelection = true;
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
    public function search(): array
    {
        return ['name'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Select::make('Tipo', 'type')
                ->options([
                    'seguro' => 'Seguro',
                    'acessorio' => 'Acessé³rio',
                    'servico' => 'Servié§o',
                ]),
            Number::make('Dié¡ria (R$)', 'daily_rate')
                ->sortable(),
            Switcher::make('Ativo', 'is_active')
                ->sortable()
                ->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Extra de Locação', [
                ID::make(),
                Text::make('Nome', 'name')->required(),
                Select::make('Tipo', 'type')
                    ->options([
                        'seguro' => 'Seguro',
                        'acessorio' => 'Acessé³rio',
                        'servico' => 'Servié§o',
                    ])
                    ->required(),
                Number::make('Dié¡ria (R$)', 'daily_rate')
                    ->step(0.01)->min(0)->required(),
                Textarea::make('Descrição', 'description'),
                Switcher::make('Ativo', 'is_active')
                    ->default(true),
            ]),
        ];
    }
    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
    protected function filters(): iterable
    {
        return [
            Select::make('Tipo', 'type')
                ->options([
                    'seguro' => 'Seguro',
                    'acessorio' => 'Acessé³rio',
                    'servico' => 'Servié§o',
                ])
                ->nullable(),
            Switcher::make('Ativo', 'is_active'),
        ];
    }
    protected function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
