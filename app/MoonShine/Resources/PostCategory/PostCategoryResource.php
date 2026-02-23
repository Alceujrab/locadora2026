<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\PostCategory;

use Illuminate\Database\Eloquent\Model;
use App\Models\PostCategory;
use App\MoonShine\Resources\PostCategory\Pages\PostCategoryIndexPage;
use App\MoonShine\Resources\PostCategory\Pages\PostCategoryFormPage;
use App\MoonShine\Resources\PostCategory\Pages\PostCategoryDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<PostCategory, PostCategoryIndexPage, PostCategoryFormPage, PostCategoryDetailPage>
 */
class PostCategoryResource extends ModelResource
{
    protected string $model = PostCategory::class;

    protected string $title = 'Categorias do Blog';

    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('Nome', 'name'),
            \MoonShine\UI\Fields\Text::make('Slug', 'slug'),
            \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active')->updateOnPreview(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Text::make('Nome', 'name')->required(),
                \MoonShine\UI\Fields\Text::make('Slug (URL)', 'slug')->required(),
                \MoonShine\UI\Fields\Textarea::make('Descrição', 'description')->hideOnIndex(),
                \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active')->default(true),
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
            PostCategoryIndexPage::class,
            PostCategoryFormPage::class,
            PostCategoryDetailPage::class,
        ];
    }
}
