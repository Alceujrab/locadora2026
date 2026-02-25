<?php

namespace App\Filament\Resources\MaintenanceAlertResource\Pages;

use App\Filament\Resources\MaintenanceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMaintenanceAlerts extends ListRecords
{
    protected static string $resource = MaintenanceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
