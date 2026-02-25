<?php

namespace App\Filament\Resources\VehicleInspectionResource\Pages;

use App\Filament\Resources\VehicleInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVehicleInspection extends EditRecord
{
    protected static string $resource = VehicleInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
