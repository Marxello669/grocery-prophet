<?php

namespace App\Enum;

enum UnitEnum: string
{
    case kg = 'kg';
    case l = 'l';
    case unit = 'unit';

    public function label(): string
    {
        return match ($this) {
            self::kg => "Kg",
            self::l => "L",
            self::unit => "Unidade",
        };
    }
}
