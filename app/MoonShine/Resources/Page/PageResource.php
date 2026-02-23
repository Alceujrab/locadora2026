<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Page;

use Illuminate\Database\Eloquent\Model;
use App\Models\Page;
use App\MoonShine\Resources\Page\Pages\PageIndexPage;
use App\MoonShine\Resources\Page\Pages\PageFormPage;
use App\MoonShine\Resources\Page\Pages\PageDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Page, PageIndexPage, PageFormPage, PageDetailPage>
 */
class PageResource extends ModelResource
{
    protected string $model = Page::class;

    protected string $title = 'Páginas Institucionais';

    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('Título', 'title'),
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
                \MoonShine\UI\Fields\TinyMce::make('Conteúdo HTML', 'content')->hideOnIndex(),
                \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->default(true),
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return $this->formFields();
    }
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            PageIndexPage::class,
            PageFormPage::class,
            PageDetailPage::class,
        ];
    }
}
