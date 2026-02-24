<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SeoMetadata;
use Illuminate\Database\Eloquent\Model;
use App\Models\SeoMetadata;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
/**
 * @extends ModelResource<SeoMetadata>
 */
class SeoMetadataResource extends ModelResource
{
    protected string $model = SeoMetadata::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'SEO: Metadados (URLs)';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('URL', 'url')->sortable(),
            \MoonShine\UI\Fields\Text::make('Té­tulo Principal', 'title'),
            \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active')->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make('Metadados Globais', [
                \MoonShine\UI\Fields\Text::make('URL Relativa', 'url')
                    ->hint('Ex: /, /frota, /contato, /sobre')
                    ->required(),
                \MoonShine\UI\Fields\Text::make('Té­tulo SEO (Meta Title)', 'title')
                    ->hint('Tamanho ideal: até© 60 caracteres')
                    ->required(),
                \MoonShine\UI\Fields\Textarea::make('Descrição (Meta Description)', 'description')
                    ->hint('Tamanho ideal: entre 150 e 160 caracteres. Este é© o resumo exibido no Google.')
                    ->required(),
                \MoonShine\UI\Fields\Text::make('Palavras-chave (Keywords)', 'keywords')
                    ->hint('Separe por vé­rgulas (Carros, Locadora, Aluguel)')
                    ->nullable(),
                \MoonShine\UI\Fields\Image::make('Imagem Facebook/App (Open Graph)', 'og_image')
                    ->dir('seo')
                    ->removable()
                    ->nullable(),
                \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active')->default(true),
            ])
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ...$this->formFields(),
            \MoonShine\ChangeLog\Components\ChangeLog::make('Histé³rico de Modificações SEO'),
        ];
    }
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
}
