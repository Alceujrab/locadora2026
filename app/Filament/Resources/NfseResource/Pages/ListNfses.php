<?php

namespace App\Filament\Resources\NfseResource\Pages;

use App\Filament\Resources\NfseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNfses extends ListRecords
{
    protected static string $resource = NfseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
