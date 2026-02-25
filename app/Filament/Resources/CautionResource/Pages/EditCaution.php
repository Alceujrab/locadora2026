<?php

namespace App\Filament\Resources\CautionResource\Pages;

use App\Filament\Resources\CautionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCaution extends EditRecord
{
    protected static string $resource = CautionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
