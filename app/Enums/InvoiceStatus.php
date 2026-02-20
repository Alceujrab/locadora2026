<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case OPEN = 'aberta';
    case PAID = 'paga';
    case OVERDUE = 'vencida';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberta',
            self::PAID => 'Paga',
            self::OVERDUE => 'Vencida',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::PAID => 'success',
            self::OVERDUE => 'danger',
            self::CANCELLED => 'secondary',
        };
    }
}
