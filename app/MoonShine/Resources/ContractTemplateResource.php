<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use App\Models\ContractTemplate;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Components\Layout\Box;

/**
 * @extends ModelResource<ContractTemplate>
 */
class ContractTemplateResource extends ModelResource
{
    protected string $model = ContractTemplate::class;

    protected string $title = 'Templates de Contrato';

    protected string $column = 'name';

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
            Switcher::make('Padrão', 'is_default')
                ->sortable(),
            Switcher::make('Ativo', 'is_active')
                ->sortable()
                ->updateOnPreview(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            Box::make('Template', [
                ID::make(),
                Text::make('Nome', 'name')->required(),
                Textarea::make('Conteúdo', 'content')
                    ->required(),
                Text::make('Variáveis', 'variables')
                    ->hint('Separar por vírgula: {{cliente_nome}}, {{veiculo_placa}}'),
            ]),

            Box::make('Status', [
                Switcher::make('Padrão', 'is_default'),
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
        ];
    }

    protected function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ];
    }
}
