<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\VehicleCategory;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends ModelResource<VehicleCategory>
 */
class VehicleCategoryResource extends ModelResource
{
    protected string $model = VehicleCategory::class;

    protected string $title = 'Categorias de Veículos';

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
            Number::make('Diária', 'daily_rate')
                ->sortable(),
            Number::make('Semanal', 'weekly_rate'),
            Number::make('Mensal', 'monthly_rate'),
            Text::make('Km', 'km_type'),
            Switcher::make('Ativo', 'is_active')
                ->sortable()
                ->updateOnPreview(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make('Categoria', [
                ID::make(),
                Text::make('Nome', 'name')->required(),
                Textarea::make('Descrição', 'description'),
            ]),

            Box::make('Preços', [
                Number::make('Diária (R$)', 'daily_rate')
                    ->step(0.01)
                    ->min(0)
                    ->required(),
                Number::make('Semanal (R$)', 'weekly_rate')
                    ->step(0.01)
                    ->min(0),
                Number::make('Mensal (R$)', 'monthly_rate')
                    ->step(0.01)
                    ->min(0),
            ]),

            Box::make('Política de Km', [
                Select::make('Tipo Km', 'km_type')
                    ->options([
                        'livre' => 'Km Livre',
                        'controlado' => 'Km Controlado',
                    ])
                    ->default('livre'),
                Number::make('Km Incluso/Dia', 'km_included')
                    ->step(1)
                    ->min(0),
                Number::make('Valor Km Excedente (R$)', 'km_extra_rate')
                    ->step(0.01)
                    ->min(0),
            ]),

            Box::make('Status', [
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
            Switcher::make('Ativo', 'is_active'),
            Select::make('Tipo Km', 'km_type')
                ->options([
                    'livre' => 'Km Livre',
                    'controlado' => 'Km Controlado',
                ])
                ->nullable(),
        ];
    }

    protected function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
        ];
    }
}
