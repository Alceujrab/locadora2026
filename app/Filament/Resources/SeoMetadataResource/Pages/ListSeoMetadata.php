<?php

namespace App\Filament\Resources\SeoMetadataResource\Pages;

use App\Filament\Resources\SeoMetadataResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeoMetadata extends ListRecords
{
    protected static string $resource = SeoMetadataResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
