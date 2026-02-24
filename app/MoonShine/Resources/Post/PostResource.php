<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Post;
use Illuminate\Database\Eloquent\Model;
use App\Models\Post;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<Post, PostIndexPage, PostFormPage, PostDetailPage>
 */
class PostResource extends ModelResource
{
    protected string $model = Post::class;
    protected string $title = 'NotÃ­cias / Blog';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Image::make('Capa', 'image')->dir('posts'),
            \MoonShine\UI\Fields\Text::make('TÃ­tulo', 'title'),
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Categoria', 'postCategory', resource: \App\MoonShine\Resources\PostCategory\PostCategoryResource::class)->badge('primary'),
            \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Image::make('Imagem de Capa', 'image')->dir('posts')->removable(),
                \MoonShine\UI\Fields\Text::make('TÃ­tulo', 'title')->required(),
                \MoonShine\UI\Fields\Text::make('Slug (URL)', 'slug')->required(),
                \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Categoria', 'postCategory', resource: \App\MoonShine\Resources\PostCategory\PostCategoryResource::class)->nullable(),
                \MoonShine\UI\Fields\TinyMce::make('ConteÃºdo HTML', 'content')->hideOnIndex(),
                \MoonShine\UI\Fields\Switcher::make('Publicado', 'is_published')->default(true),
            ])
        ];
    }
    protected function detailFields(): iterable
    {
        return [
            ...$this->formFields(),
            \MoonShine\ChangeLog\Components\ChangeLog::make('HistÃ³rico de AtualizaÃ§Ãµes'),
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
