<?php

namespace App\Filament\Resources\CautionResource\Pages;

use App\Filament\Resources\CautionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCautions extends ListRecords
{
    protected static string $resource = CautionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
