<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case OPEN = 'aberta';
    case IN_PROGRESS = 'em_andamento';
    case AWAITING_SIGNATURE = 'aguardando_assinatura';
    case COMPLETED = 'concluida';
    case CLOSED = 'fechada';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberta',
            self::IN_PROGRESS => 'Em Andamento',
            self::AWAITING_SIGNATURE => 'Aguardando Assinatura',
            self::COMPLETED => 'Concluída',
            self::CLOSED => 'Fechada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::IN_PROGRESS => 'primary',
            self::AWAITING_SIGNATURE => 'warning',
            self::COMPLETED => 'success',
            self::CLOSED => 'gray',
            self::CANCELLED => 'danger',
        };
    }
}
