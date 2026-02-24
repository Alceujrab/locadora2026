<?php

declare(strict_types=1);

namespace App\MoonShine\Pages\Contract;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Support\Facades\Storage;
use MoonShine\Contracts\UI\ActionButtonContract;
use MoonShine\Laravel\Pages\Crud\IndexPage;
use MoonShine\Support\Enums\HttpMethod;
use MoonShine\Support\ListOf;
use MoonShine\UI\Components\ActionButton;

class ContractIndexPage extends IndexPage
{
    /**
     * Sobrescreve os botões por linha da tabela para incluir
     * tanto os botões padrão (view/edit/delete) quanto os de negócio.
     */
    protected function buttons(): ListOf
    {
        // Botões padrão do MoonShine (view, edit, delete, massDelete)
        $defaultButtons = parent::buttons();

        // Botões customizados de negócio
        $customButtons = [
            ActionButton::make('Gerar PDF', fn(Contract $item) => route('admin.contract.generatePdf', $item->id))
                ->icon('document-text')
                ->primary()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => $item?->template_id !== null),

            ActionButton::make('Baixar PDF', fn(Contract $item) => ($item?->pdf_path) ? Storage::disk('public')->url($item->pdf_path) : '#')
                ->icon('arrow-down-tray')
                ->blank()
                ->canSee(fn($item) => ($item?->pdf_path ?? null) !== null),

            ActionButton::make('Check-out (Entrega)', fn(Contract $item) => route('admin.contract.checkout', $item->id))
                ->icon('truck')
                ->success()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => in_array($item?->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE])),

            ActionButton::make('Check-in (Devolução)', fn(Contract $item) => route('admin.contract.checkin', $item->id))
                ->icon('arrow-uturn-left')
                ->warning()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => $item?->status === ContractStatus::ACTIVE),

            ActionButton::make('Gerar Fatura', fn(Contract $item) => route('admin.contract.generateInvoices', $item->id))
                ->icon('banknotes')
                ->warning()
                ->async(HttpMethod::POST)
                ->canSee(fn($item) => in_array($item?->status, [ContractStatus::ACTIVE, ContractStatus::FINISHED]) && !$item?->invoices()?->exists()),
        ];

        // Mescla: custom primeiro, depois os padrões
        return new ListOf(ActionButtonContract::class, [
            ...$customButtons,
            ...$defaultButtons->toArray(),
        ]);
    }
}
