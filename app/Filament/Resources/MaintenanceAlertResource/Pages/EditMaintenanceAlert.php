<?php

namespace App\Filament\Resources\MaintenanceAlertResource\Pages;

use App\Filament\Resources\MaintenanceAlertResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMaintenanceAlert extends EditRecord
{
    protected static string $resource = MaintenanceAlertResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
