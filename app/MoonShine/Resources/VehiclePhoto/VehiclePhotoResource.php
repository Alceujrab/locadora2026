<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehiclePhoto;
use Illuminate\Database\Eloquent\Model;
use App\Models\VehiclePhoto;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;
/**
 * @extends ModelResource<VehiclePhoto, VehiclePhotoIndexPage, VehiclePhotoFormPage, VehiclePhotoDetailPage>
 */
class VehiclePhotoResource extends ModelResource
{
    protected string $model = VehiclePhoto::class;
    protected string $title = 'Fotos do Veé­culo';
    protected function indexFields(): iterable
    {
        return [
            \MoonShine\UI\Fields\ID::make()->sortable(),
            \MoonShine\UI\Fields\Image::make('Foto', 'path')->dir('vehicles'),
            \MoonShine\UI\Fields\Text::make('Legenda', 'filename'),
            \MoonShine\UI\Fields\Switcher::make('Capa', 'is_cover')->updateOnPreview(),
        ];
    }
    protected function formFields(): iterable
    {
        return [
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Veé­culo', 'vehicle', resource: \App\MoonShine\Resources\VehicleResource::class),
            \MoonShine\UI\Fields\Image::make('Foto', 'path')->dir('vehicles')->removable()->required(),
            \MoonShine\UI\Fields\Text::make('Legenda', 'filename'),
            \MoonShine\UI\Fields\Number::make('Posição', 'position')->default(0),
            \MoonShine\UI\Fields\Switcher::make('Foto de Capa', 'is_cover'),
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

