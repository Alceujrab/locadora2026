<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class FleetProfitabilityPage extends Page
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | \UnitEnum | null $navigationGroup = 'Relatorios';

    protected static ?string $title = 'Lucratividade da Frota';

    protected string $view = 'filament.pages.fleet-profitability-page';

    public function getTitle(): string|Htmlable
    {
        return 'Lucratividade da Frota (Em Breve)';
    }

    // O back-end para relatorio sera inserido nas proximas etapas
}
