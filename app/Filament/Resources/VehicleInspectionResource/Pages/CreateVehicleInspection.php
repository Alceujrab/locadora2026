<?php

namespace App\Filament\Resources\VehicleInspectionResource\Pages;

use App\Filament\Resources\VehicleInspectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVehicleInspection extends CreateRecord
{
    protected static string $resource = VehicleInspectionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['inspector_user_id'] = auth()->id() ?? 1;

        return $data;
    }
}
