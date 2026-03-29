<?php

namespace App\Enum;

enum GroceryEnum: string
{
    case meat = 'meat';
    case beverage = 'beverage';
    case fruit = 'fruit';
    case fish = 'fish';

    public function label(): string
    {
        return match ($this) {
            self::meat => "Carne",
            self::beverage => "Bebida",
            self::fruit => "Fruta",
            self::fish => "Peixe",
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::meat => "🥩",
            self::beverage => "🧃",
            self::fruit => "🍎",
            self::fish => "🐟"
        };
    }
}
