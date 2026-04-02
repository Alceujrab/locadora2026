<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum InspectionType: string implements HasLabel
{
    case CHECKOUT = 'saida';
    case RETURN = 'retorno';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::CHECKOUT => 'Check-out (Devolucao)',
            self::RETURN => 'Check-in (Saida do veiculo)',
        };
    }

    public function label(): string
    {
        return $this->getLabel() ?? $this->value;
    }
}
