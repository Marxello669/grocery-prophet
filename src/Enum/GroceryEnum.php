<?php

namespace App\Enum;

enum GroceryEnum: string
{
    case meat = 'meat';
    case beverage = 'beverage';
    case fruit = 'fruit';
    case fish = 'fish';
    case rice_pasta_flour = "rice_pasta_flour";

    public function label(): string
    {
        return match ($this) {
            self::meat => "Carne",
            self::beverage => "Bebida",
            self::fruit => "Fruta",
            self::fish => "Peixe",
            self::rice_pasta_flour => "Arroz, Esparguete e Farinha",
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::meat => "🥩",
            self::beverage => "🧃",
            self::fruit => "🍎",
            self::fish => "🐟",
            self::rice_pasta_flour => "-"
        };
    }
}
