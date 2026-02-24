<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;
use App\Models\Branch;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\UI\Fields\ID;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Image;
use MoonShine\UI\Fields\Phone;
use MoonShine\UI\Fields\Email;
use MoonShine\UI\Fields\Switcher;
use MoonShine\UI\Fields\Textarea;
use MoonShine\UI\Components\Layout\Box;
/**
 * @extends ModelResource<Branch>
 */
class BranchResource extends ModelResource
{
    protected string $model = Branch::class;
    protected string $title = 'Filiais';
    protected string $column = 'name';
    protected bool $columnSelection = true;
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
    /**
     * @return string[]
     */
    public function search(): array
    {
        return ['name', 'cnpj', 'city', 'email'];
    }
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Nome', 'name')->sortable(),
            Text::make('CNPJ', 'cnpj'),
            Text::make('Cidade', 'city'),
            Text::make('UF', 'state'),
            Phone::make('Telefone', 'phone'),
            Switcher::make('Ativo', 'is_active')
                ->sortable()
                ->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            Box::make('Dados da Filial', [
                ID::make(),
                Image::make('Logomarca', 'logo')
                    ->dir('branches')
                    ->removable(),
                Text::make('Nome', 'name')
                    ->required(),
                Text::make('CNPJ', 'cnpj'),
                Email::make('E-mail', 'email'),
                Phone::make('Telefone', 'phone'),
                Phone::make('WhatsApp', 'whatsapp'),
            ]),
            Box::make('EndereÃ§o', [
                Text::make('CEP', 'zip_code'),
                Text::make('Rua', 'street'),
                Text::make('NÃºmero', 'number'),
                Text::make('Complemento', 'complement'),
                Text::make('Bairro', 'neighborhood'),
                Text::make('Cidade', 'city'),
                Text::make('UF', 'state'),
            ]),
            Box::make('Status', [
                Switcher::make('Ativo', 'is_active')
                    ->default(true),
            ]),
            Box::make('ObservaÃ§Ãµes', [
                Textarea::make('ObservaÃ§Ãµes', 'notes'),
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
            Text::make('Cidade', 'city'),
            Text::make('UF', 'state'),
        ];
    }
    protected function rules($item): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['nullable', 'string', 'max:18'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
