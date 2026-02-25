<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContract extends EditRecord
{
    protected static string $resource = ContractResource::class;

    protected function getHeaderActions(): array
    {
        // Re-use the existing actions defined in the resource table for consistency in the edit header
        return array_merge(
            ContractResource::getCustomActions(),
            [Actions\DeleteAction::make()]
        );
    }
}
