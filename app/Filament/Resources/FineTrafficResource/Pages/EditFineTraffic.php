<?php

namespace App\Filament\Resources\FineTrafficResource\Pages;

use App\Filament\Resources\FineTrafficResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFineTraffic extends EditRecord
{
    protected static string $resource = FineTrafficResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
