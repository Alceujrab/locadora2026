<?php

namespace App\Filament\Resources\ContractResource\Pages;

use App\Filament\Resources\ContractResource;
use App\Models\Contract;
use Filament\Resources\Pages\CreateRecord;

class CreateContract extends CreateRecord
{
    protected static string $resource = ContractResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['contract_number'])) {
            $data['contract_number'] = Contract::generateContractNumber();
        }
        $data['created_by'] = auth()->id() ?? 1;

        return $data;
    }
}
