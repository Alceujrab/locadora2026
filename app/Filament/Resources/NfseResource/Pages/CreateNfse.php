<?php

namespace App\Filament\Resources\NfseResource\Pages;

use App\Filament\Resources\NfseResource;
use App\Models\Nfse;
use Filament\Resources\Pages\CreateRecord;

class CreateNfse extends CreateRecord
{
    protected static string $resource = NfseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['numero'])) {
            $data['numero'] = Nfse::generateNumero();
        }

        if (isset($data['valor_servico']) && isset($data['aliquota_iss']) && empty($data['valor_iss'])) {
            $data['valor_iss'] = round((float) $data['valor_servico'] * ((float) $data['aliquota_iss'] / 100), 2);
        }

        return $data;
    }
}
