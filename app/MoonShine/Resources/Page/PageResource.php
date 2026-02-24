<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page;
use Illuminate\Database\Eloquent\Model;
use App\Models\Page;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<Page, PageIndexPage, PageFormPage, PageDetailPage>
 */
class PageResource extends ModelResource
{
    protected string $model = Page::class;
    protected string $title = 'Pé¡ginas Institucionais';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('Té­tulo', 'title'),
            \MoonShine\UI\Fields\Text::make('Slug', 'slug'),
            \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Text::make('Título', 'title')->required(),
                \MoonShine\UI\Fields\Text::make('Slug (URL)', 'slug')->required(),
                \MoonShine\TinyMce\Fields\TinyMce::make('Conteúdo HTML', 'content'),
                \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->default(true),
            ])
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ...$this->formFields(),
            \MoonShine\ChangeLog\Components\ChangeLog::make('Histé³rico de Atualizações'),
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
