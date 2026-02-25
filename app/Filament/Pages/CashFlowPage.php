<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class CashFlowPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static string | \UnitEnum | null $navigationGroup = 'Relatorios';

    protected static ?string $title = 'Fluxo de Caixa';

    protected string $view = 'filament.pages.cash-flow-page';

    public function getTitle(): string|Htmlable
    {
        return 'Fluxo de Caixa (Em Breve)';
    }

    // O back-end para relatorio sera inserido nas proximas etapas
}
