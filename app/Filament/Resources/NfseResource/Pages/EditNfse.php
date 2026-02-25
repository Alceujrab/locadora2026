<?php

namespace App\Filament\Resources\NfseResource\Pages;

use App\Filament\Resources\NfseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNfse extends EditRecord
{
    protected static string $resource = NfseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
