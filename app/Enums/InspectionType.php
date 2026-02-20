<?php

namespace App\Enums;

enum InspectionType: string
{
    case CHECKOUT = 'saida';
    case RETURN = 'retorno';

    public function label(): string
    {
        return match ($this) {
            self::CHECKOUT => 'Saída',
            self::RETURN => 'Retorno',
        };
    }
}
