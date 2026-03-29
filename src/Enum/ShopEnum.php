<?php

namespace App\Enum;

enum ShopEnum: string
{
    case continente = 'continente';
    case rei_dos_precos = 'rei_dos_precos';
    case canario = 'canario';
    case lidl = 'lidl';
    case pingo_doce = 'pingo_doce';

    public function label(): string
    {
        return match ($this) {
            self::continente => "Continente",
            self::rei_dos_precos => "Rei dos Preços",
            self::canario => "Canário",
            self::lidl => "Lidl",
            self::pingo_doce => "Pingo Doce",
        };
    }
}
