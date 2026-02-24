<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Post;

use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use App\MoonShine\Resources\Post\Pages\PostIndexPage;
use App\MoonShine\Resources\Post\Pages\PostFormPage;
use App\MoonShine\Resources\Post\Pages\PostDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Post, PostIndexPage, PostFormPage, PostDetailPage>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class;

    protected string $title = 'Notícias / Blog';

    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Image::make('Capa', 'image')->dir('posts'),
            \MoonShine\UI\Fields\Text::make('Título', 'title'),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Categoria', 'postCategory', resource: \App\MoonShine\Resources\PostCategory\PostCategoryResource::class)->badge('primary'),
            \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->updateOnPreview(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Image::make('Imagem de Capa', 'image')->dir('posts')->removable(),
                \MoonShine\UI\Fields\Text::make('Título', 'title')->required(),
                \MoonShine\UI\Fields\Text::make('Slug (URL)', 'slug')->required(),
                \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Categoria', 'postCategory', resource: \App\MoonShine\Resources\PostCategory\PostCategoryResource::class)->nullable(),
                \MoonShine\UI\Fields\TinyMce::make('Conteúdo HTML', 'content')->hideOnIndex(),
                \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->default(true),
            ])
        ];
    }

    protected function detailFields(): iterable
    {
        return [
            ...$this->formFields(),
            \MoonShine\ChangeLog\Components\ChangeLog::make('Histórico de Atualizações'),
        ];
    }
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            PostIndexPage::class,
            PostFormPage::class,
            PostDetailPage::class,
        ];
    }
}
