<?php

namespace App\Filament\Resources\FineTrafficResource\Pages;

use App\Filament\Resources\FineTrafficResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFineTraffics extends ListRecords
{
    protected static string $resource = FineTrafficResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
