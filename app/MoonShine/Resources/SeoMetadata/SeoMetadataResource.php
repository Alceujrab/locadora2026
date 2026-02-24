<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SeoMetadata;
use Illuminate\Database\Eloquent\Model;
use App\Models\SeoMetadata;
use MoonShine\Laravel\Resources\ModelResource;
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
    protected string $title = 'SEO: Metadados (URLs)';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('URL', 'url')->sortable(),
            \MoonShine\UI\Fields\Text::make('TÃ­tulo Principal', 'title'),
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
                \MoonShine\UI\Fields\Text::make('TÃ­tulo SEO (Meta Title)', 'title')
                    ->hint('Tamanho ideal: atÃ© 60 caracteres')
                    ->required(),
                \MoonShine\UI\Fields\Textarea::make('DescriÃ§Ã£o (Meta Description)', 'description')
                    ->hint('Tamanho ideal: entre 150 e 160 caracteres. Este Ã© o resumo exibido no Google.')
                    ->required(),
                \MoonShine\UI\Fields\Text::make('Palavras-chave (Keywords)', 'keywords')
                    ->hint('Separe por vÃ­rgulas (Carros, Locadora, Aluguel)')
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
            \MoonShine\ChangeLog\Components\ChangeLog::make('HistÃ³rico de ModificaÃ§Ãµes SEO'),
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
