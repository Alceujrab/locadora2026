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
            Actions\Action::make('downloadFici')
                ->label('Baixar FICI (PDF)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->visible(fn () => ! empty($this->record?->driver_name) && ! empty($this->record?->driver_cpf))
                ->url(fn () => route('admin.fines-traffic.fici', ['id' => $this->record->id]), shouldOpenInNewTab: true),
            Actions\DeleteAction::make(),
        ];
    }
}

