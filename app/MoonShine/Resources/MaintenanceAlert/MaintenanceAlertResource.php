<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MaintenanceAlert;

use Illuminate\Database\Eloquent\Model;
use App\Models\MaintenanceAlert;
use App\MoonShine\Resources\MaintenanceAlert\Pages\MaintenanceAlertIndexPage;
use App\MoonShine\Resources\MaintenanceAlert\Pages\MaintenanceAlertFormPage;
use App\MoonShine\Resources\MaintenanceAlert\Pages\MaintenanceAlertDetailPage;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\Contracts\Core\PageContract;

/**
 * @extends ModelResource<MaintenanceAlert, MaintenanceAlertIndexPage, MaintenanceAlertFormPage, MaintenanceAlertDetailPage>
 */
class MaintenanceAlertResource extends ModelResource
{
    protected string $model = MaintenanceAlert::class;

    protected string $title = 'Alertas de Manutenção';
    
    protected string $column = 'id';

    public function iconValue(): string
    {
        return 'heroicons.outline.wrench';
    }
    
    /**
     * @return list<class-string<PageContract>>
     */
    protected function pages(): array
    {
        return [
            MaintenanceAlertIndexPage::class,
            MaintenanceAlertFormPage::class,
            MaintenanceAlertDetailPage::class,
        ];
    }
}
