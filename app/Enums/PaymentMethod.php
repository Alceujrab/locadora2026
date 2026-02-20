<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case PIX = 'pix';
    case CREDIT_CARD = 'cartao';
    case BOLETO = 'boleto';
    case CASH = 'dinheiro';
    case TRANSFER = 'transferencia';

    public function label(): string
    {
        return match ($this) {
            self::PIX => 'PIX',
            self::CREDIT_CARD => 'Cartão de Crédito',
            self::BOLETO => 'Boleto',
            self::CASH => 'Dinheiro',
            self::TRANSFER => 'Transferência',
        };
    }
}
