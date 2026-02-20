<?php

namespace App\Enums;

enum ContractStatus: string
{
    case DRAFT = 'rascunho';
    case AWAITING_SIGNATURE = 'aguardando_assinatura';
    case ACTIVE = 'ativo';
    case FINISHED = 'finalizado';
    case CANCELLED = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Rascunho',
            self::AWAITING_SIGNATURE => 'Aguardando Assinatura',
            self::ACTIVE => 'Ativo',
            self::FINISHED => 'Finalizado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::AWAITING_SIGNATURE => 'warning',
            self::ACTIVE => 'success',
            self::FINISHED => 'info',
            self::CANCELLED => 'danger',
        };
    }
}
