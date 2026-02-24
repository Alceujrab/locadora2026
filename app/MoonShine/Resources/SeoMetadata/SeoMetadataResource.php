<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\SeoMetadata;

use Illuminate\Database\Eloquent\Model;
use App\Models\SeoMetadata;
use App\MoonShine\Resources\SeoMetadata\Pages\SeoMetadataIndexPage;
use App\MoonShine\Resources\SeoMetadata\Pages\SeoMetadataFormPage;
use App\MoonShine\Resources\SeoMetadata\Pages\SeoMetadataDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<SeoMetadata, SeoMetadataIndexPage, SeoMetadataFormPage, SeoMetadataDetailPage>
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
            \MoonShine\UI\Fields\Text::make('Título Principal', 'title'),
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
                \MoonShine\UI\Fields\Text::make('Título SEO (Meta Title)', 'title')
                    ->hint('Tamanho ideal: até 60 caracteres')
                    ->required(),
                \MoonShine\UI\Fields\Textarea::make('Descrição (Meta Description)', 'description')
                    ->hint('Tamanho ideal: entre 150 e 160 caracteres. Este é o resumo exibido no Google.')
                    ->required(),
                \MoonShine\UI\Fields\Text::make('Palavras-chave (Keywords)', 'keywords')
                    ->hint('Separe por vírgulas (Carros, Locadora, Aluguel)')
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
            \MoonShine\ChangeLog\Components\ChangeLog::make('Histórico de Modificações SEO'),
        ];
    }

    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            SeoMetadataIndexPage::class,
            SeoMetadataFormPage::class,
            SeoMetadataDetailPage::class,
        ];
    }
}
