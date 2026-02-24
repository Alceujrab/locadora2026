<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Supplier;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Select;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\Laravel\Fields\Relationships\BelongsTo;
/**
 * @extends ModelResource<Supplier>
 */
class SupplierResource extends ModelResource
{
    protected string $model = Supplier::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'Fornecedores';
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
        return ['name', 'cnpj', 'contact_name', 'email', 'phone'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Select::make('Tipo', 'type')
                ->options([
                    'oficina' => 'Oficina',
                    'pecas' => 'Peé§as',
                    'ambos' => 'Ambos',
                ]),
            Text::make('Contato', 'contact_name'),
            Phone::make('Telefone', 'phone'),
            Text::make('Cidade', 'city'),
            Switcher::make('Ativo', 'is_active')
                ->sortable()
                ->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Dados do Fornecedor', [
                ID::make(),
                BelongsTo::make('Filial', 'branch', resource: BranchResource::class),
                Text::make('Nome', 'name')->required(),
                Text::make('CNPJ', 'cnpj'),
                Select::make('Tipo', 'type')
                    ->options([
                        'oficina' => 'Oficina',
                        'pecas' => 'Peé§as',
                        'ambos' => 'Ambos',
                    ])
                    ->required(),
            ]),
            Box::make('Contato', [
                Text::make('Nome Contato', 'contact_name'),
                Phone::make('Telefone', 'phone'),
                Email::make('E-mail', 'email'),
            ]),
            Box::make('Endereé§o', [
                Text::make('CEP', 'zip_code'),
                Text::make('Rua', 'street'),
                Text::make('Néºmero', 'number'),
                Text::make('Complemento', 'complement'),
                Text::make('Bairro', 'neighborhood'),
                Text::make('Cidade', 'city'),
                Text::make('UF', 'state'),
            ]),
            Box::make('Informações', [
                Textarea::make('Especialidades', 'specialties'),
                Textarea::make('Observações', 'notes'),
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
                    'oficina' => 'Oficina',
                    'pecas' => 'Peé§as',
                    'ambos' => 'Ambos',
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
        ];
    }
}
