<?php

namespace App\Enum;

enum UnitEnum: string
{
    case kg = 'kg';
    case l = 'l';
    case unit = 'unit';

    case g = 'g';
    case mg = 'mg';
    case ml = 'ml';

    public function label(): string
    {
        return match ($this) {
            self::kg => "Kg",
            self::g => "g",
            self::mg => "mg",
            self::l => "L",
            self::ml => "ml",
            self::unit => "Uni",
        };
    }

    /**
     * @return UnitEnum[]
     */
    public function getSubUnits(): array
    {
        return match ($this) {
            self::kg, self::g, self::mg => [self::kg, self::g, self::mg],
            self::l, self::ml => [self::l, self::ml],
            self::unit => [self::unit],
        };
    }

    public function getFactor(): float
    {
        return match ($this) {
            self::kg, self::l, self::unit => 1.0,
            self::g, self::ml => 0.001,
            self::mg => 0.000001,
        };
    }
}
