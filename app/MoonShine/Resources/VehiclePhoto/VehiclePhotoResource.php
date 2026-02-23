<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehiclePhoto;

use Illuminate\Database\Eloquent\Model;
use App\Models\VehiclePhoto;
use App\MoonShine\Resources\VehiclePhoto\Pages\VehiclePhotoIndexPage;
use App\MoonShine\Resources\VehiclePhoto\Pages\VehiclePhotoFormPage;
use App\MoonShine\Resources\VehiclePhoto\Pages\VehiclePhotoDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<VehiclePhoto, VehiclePhotoIndexPage, VehiclePhotoFormPage, VehiclePhotoDetailPage>
 */
class VehiclePhotoResource extends ModelResource
{
    protected string $model = VehiclePhoto::class;

    protected string $title = 'Fotos do Veículo';

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
            \MoonShine\Laravel\Fields\Relationships\BelongsTo::make('Veículo', 'vehicle', resource: \App\MoonShine\Resources\VehicleResource::class)->hideOnForm(),
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
            VehiclePhotoIndexPage::class,
            VehiclePhotoFormPage::class,
            VehiclePhotoDetailPage::class,
        ];
    }
}
