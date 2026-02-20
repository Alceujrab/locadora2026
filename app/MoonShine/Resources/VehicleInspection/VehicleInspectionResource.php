<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\VehicleInspection;

use Illuminate\Database\Eloquent\Model;
use App\Models\VehicleInspection;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionIndexPage;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionFormPage;
use App\MoonShine\Resources\VehicleInspection\Pages\VehicleInspectionDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<VehicleInspection, VehicleInspectionIndexPage, VehicleInspectionFormPage, VehicleInspectionDetailPage>
 */
class VehicleInspectionResource extends ModelResource
{
    protected string $model = VehicleInspection::class;

    protected string $title = 'VehicleInspections';
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            VehicleInspectionIndexPage::class,
            VehicleInspectionFormPage::class,
            VehicleInspectionDetailPage::class,
        ];
    }
}
