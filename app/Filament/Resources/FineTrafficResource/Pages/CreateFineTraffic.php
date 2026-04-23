<?php

namespace App\Filament\Resources\FineTrafficResource\Pages;

use App\Filament\Resources\FineTrafficResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFineTraffic extends CreateRecord
{
    protected static string $resource = FineTrafficResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Suporte a lançamento direto do dashboard do veículo: /admin/fine-traffic/create?vehicle_id=123
        $vehicleId = request()->query('vehicle_id');
        if (! empty($vehicleId) && empty($data['vehicle_id'])) {
            $data['vehicle_id'] = $vehicleId;
        }
        return $data;
    }

    protected function fillForm(): void
    {
        $data = [];
        if ($vehicleId = request()->query('vehicle_id')) {
            $data['vehicle_id'] = $vehicleId;
        }
        if ($contractId = request()->query('contract_id')) {
            $data['contract_id'] = $contractId;
        }
        if ($customerId = request()->query('customer_id')) {
            $data['customer_id'] = $customerId;
        }
        $this->form->fill($data);
    }
}

