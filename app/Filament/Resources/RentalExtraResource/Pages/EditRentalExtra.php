<?php

namespace App\Filament\Resources\RentalExtraResource\Pages;

use App\Filament\Resources\RentalExtraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalExtra extends EditRecord
{
    protected static string $resource = RentalExtraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
