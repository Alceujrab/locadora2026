<?php

namespace App\Enums;

enum VehicleStatus: string
{
    case AVAILABLE = 'disponivel';
    case RENTED = 'locado';
    case MAINTENANCE = 'manutencao';
    case RESERVED = 'reservado';
    case INACTIVE = 'inativo';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponível',
            self::RENTED => 'Locado',
            self::MAINTENANCE => 'Manutenção',
            self::RESERVED => 'Reservado',
            self::INACTIVE => 'Inativo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'success',
            self::RENTED => 'primary',
            self::MAINTENANCE => 'warning',
            self::RESERVED => 'info',
            self::INACTIVE => 'secondary',
        };
    }
}
