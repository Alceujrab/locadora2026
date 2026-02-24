<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Contract;

use App\Enums\ContractStatus;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Pages\Crud\DetailPage;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

class ContractDetailPage extends DetailPage
{
    /**
     * Sobrescreve buttons() para adicionar botões de negócio
     * (PDF, Assinar, Check-in/out, Fatura) à detail page do contrato.
     */
    protected function buttons(): ListOf
    {
        // Botões padrão (edit + delete)
        $defaultButtons = parent::buttons();

        // Botões customizados de negócio
        $customButtons = [
            ActionButton::make('Gerar PDF', '#')
                ->icon('document-text')
                ->primary()
                ->method('generatePdf')
                ->canSee(fn($item) => $item?->template_id !== null),

            ActionButton::make('Baixar PDF', fn($item) => ($item?->pdf_path) ? Storage::disk('public')->url($item->pdf_path) : '#')
                ->icon('arrow-down-tray')
                ->blank()
                ->canSee(fn($item) => ($item?->pdf_path ?? null) !== null),

            ActionButton::make('Assinar', '#')
                ->icon('pencil-square')
                ->success()
                ->method('sign')
                ->canSee(fn($item) => $item?->status === ContractStatus::AWAITING_SIGNATURE && $item?->pdf_path !== null),

            ActionButton::make('Check-out', '#')
                ->icon('truck')
                ->success()
                ->method('checkout')
                ->canSee(fn($item) => in_array($item?->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE])),

            ActionButton::make('Check-in', '#')
                ->icon('arrow-uturn-left')
                ->warning()
                ->method('checkin')
                ->canSee(fn($item) => $item?->status === ContractStatus::ACTIVE),

            ActionButton::make('Gerar Fatura', '#')
                ->icon('banknotes')
                ->warning()
                ->method('generateInvoices')
                ->canSee(fn($item) => in_array($item?->status, [ContractStatus::ACTIVE, ContractStatus::FINISHED]) && !$item?->invoices()?->exists()),
        ];

        return new ListOf(ActionButtonContract::class, [
            ...$customButtons,
            ...$defaultButtons->toArray(),
        ]);
    }
}
