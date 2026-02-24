<?php

declare(strict_types=1);

namespace App\MoonShine\Resources\MaintenanceAlert;
use Illuminate\Database\Eloquent\Model;
use App\Models\MaintenanceAlert;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Laravel\Pages\Crud\FormPage;
use MoonShine\Laravel\Pages\Crud\DetailPage;
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
            IndexPage::class,
            FormPage::class,
            DetailPage::class,
        ];
    }
}
