<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\Faq;
use Illuminate\Database\Eloquent\Model;
use App\Models\Faq;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Support\Enums\PageType;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<Faq, FaqIndexPage, FaqFormPage, FaqDetailPage>
 */
class FaqResource extends ModelResource
{
    protected string $model = Faq::class;
    protected ?PageType $redirectAfterSave = PageType::INDEX;
    protected string $title = 'FAQs (Perguntas Frequentes)';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Text::make('Pergunta', 'question'),
            \MoonShine\UI\Fields\Switcher::make('Ativo', 'is_active')->updateOnPreview(),
            \MoonShine\UI\Fields\Number::make('Posição', 'position')->sortable(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            \MoonShine\UI\Components\Layout\Box::make([
                \MoonShine\UI\Fields\Text::make('Pergunta', 'question')->required(),
                \MoonShine\UI\Fields\Textarea::make('Resposta', 'answer')->required(),
                \MoonShine\UI\Fields\Number::make('Posição/Ordem', 'position')->default(0),
                \MoonShine\UI\Fields\Switcher::make('Ativo no Site', 'is_active')->default(true),
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
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
}
