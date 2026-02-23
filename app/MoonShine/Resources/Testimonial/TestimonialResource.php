<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Testimonial;

use Illuminate\Database\Eloquent\Model;
use App\Models\Testimonial;
use App\MoonShine\Resources\Testimonial\Pages\TestimonialIndexPage;
use App\MoonShine\Resources\Testimonial\Pages\TestimonialFormPage;
use App\MoonShine\Resources\Testimonial\Pages\TestimonialDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<Testimonial, TestimonialIndexPage, TestimonialFormPage, TestimonialDetailPage>
 */
class TestimonialResource extends ModelResource
{
    protected string $model = Testimonial::class;

    protected string $title = 'Depoimentos (Testimonials)';

    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Image::make('Avatar', 'avatar')->dir('testimonials'),
            \MoonShine\UI\Fields\Text::make('Nome do Cliente', 'name'),
            \MoonShine\UI\Fields\Text::make('Empresa/Origem', 'company'),
            \MoonShine\UI\Fields\Number::make('Estrelas', 'rating')->stars(),
            \MoonShine\UI\Fields\Switcher::make('Destacado', 'is_active')->updateOnPreview(),
        ];
    }

    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Image::make('Foto/Avatar', 'avatar')->dir('testimonials')->removable(),
                \MoonShine\UI\Fields\Text::make('Nome', 'name')->required(),
                \MoonShine\UI\Fields\Text::make('Empresa, Cidade ou Cargo', 'company'),
                \MoonShine\UI\Fields\Textarea::make('Mensagem de Depoimento', 'content')->required(),
                \MoonShine\UI\Fields\Number::make('Nota (1 a 5)', 'rating')->min(1)->max(5)->default(5)->stars(),
                \MoonShine\UI\Fields\Switcher::make('Exibir no Site', 'is_active')->default(true),
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
            TestimonialIndexPage::class,
            TestimonialFormPage::class,
            TestimonialDetailPage::class,
        ];
    }
}
