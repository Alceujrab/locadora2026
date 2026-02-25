<?php

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case OPEN = 'aberta';
    case AWAITING_AUTHORIZATION = 'aguardando_autorizacao';
    case AUTHORIZED = 'autorizada';
    case IN_PROGRESS = 'em_andamento';
    case AWAITING_APPROVAL = 'aguardando_aprovacao';
    case COMPLETED = 'concluida';
    case INVOICED = 'faturada';
    case CLOSED = 'fechada';
    case CANCELLED = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'Aberta',
            self::AWAITING_AUTHORIZATION => 'Aguardando Autorizacao',
            self::AUTHORIZED => 'Autorizada',
            self::IN_PROGRESS => 'Em Andamento',
            self::AWAITING_APPROVAL => 'Aguardando Aprovacao',
            self::COMPLETED => 'Concluida',
            self::INVOICED => 'Faturada',
            self::CLOSED => 'Fechada',
            self::CANCELLED => 'Cancelada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::AWAITING_AUTHORIZATION => 'warning',
            self::AUTHORIZED => 'success',
            self::IN_PROGRESS => 'primary',
            self::AWAITING_APPROVAL => 'warning',
            self::COMPLETED => 'success',
            self::INVOICED => 'gray',
            self::CLOSED => 'gray',
            self::CANCELLED => 'danger',
        };
    }
}
