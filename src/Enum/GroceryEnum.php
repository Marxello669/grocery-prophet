<?php

namespace App\Enum;

enum GroceryEnum: string
{
    case meat = 'Carne';
    case beverage = 'beverage';
    case fruit = 'fruit';

    public function label(): string
    {
        return match ($this) {
            self::meat => "Carne",
            self::beverage => "Bebida",
            self::fruit => "Fruta",
        };
    }
}
