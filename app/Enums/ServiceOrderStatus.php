<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case OPEN = 'aberta';
    case IN_PROGRESS = 'em_andamento';
    case COMPLETED = 'concluida';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberta',
            self::IN_PROGRESS => 'Em Andamento',
            self::COMPLETED => 'Concluída',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
