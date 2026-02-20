<?php

namespace App\Enums;

enum ReservationStatus: string
{
    case PENDING = 'pendente';
    case CONFIRMED = 'confirmada';
    case IN_PROGRESS = 'em_andamento';
    case COMPLETED = 'concluida';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pendente',
            self::CONFIRMED => 'Confirmada',
            self::IN_PROGRESS => 'Em Andamento',
            self::COMPLETED => 'Concluída',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::IN_PROGRESS => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }
}
