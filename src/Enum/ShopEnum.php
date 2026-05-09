<?php

namespace App\Enum;

enum ShopEnum: string
{
    case canario = 'canario';
    case continente = 'continente';
    case intermarche = 'intermarche';
    case lidl = 'lidl';
    case mercadona = 'mercadona';
    case pingo_doce = 'pingo_doce';
    case rei_dos_precos = 'rei_dos_precos';
    case jbla = 'jbla';

    public function label(): string
    {
        return match ($this) {
            self::continente => "Continente",
            self::rei_dos_precos => "Rei dos Preços",
            self::canario => "Canário",
            self::lidl => "Lidl",
            self::pingo_doce => "Pingo Doce",
            self::intermarche => "Intermarché",
            self::mercadona => "Mercadona",
            self::jbla => "João Borges Lima Aguiar, Lda",
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::continente => "#ea2d2d",
            self::rei_dos_precos => "#ffcc00",
            self::canario => "#32cd32",
            self::lidl => "#0050aa",
            self::pingo_doce => "#00843d",
            self::intermarche => "#e21e26",
            self::mercadona => "#00a650",
            self::jbla => "#2ecc8a",
        };
    }

    /**
     * @return string[]
     */
    public static function colors(): array
    {
        return [
            self::continente->value => self::continente->color(),
            self::rei_dos_precos->value => self::rei_dos_precos->color(),
            self::canario->value => self::canario->color(),
            self::lidl->value => self::lidl->color(),
            self::pingo_doce->value => self::pingo_doce->color(),
            self::intermarche->value => self::intermarche->color(),
            self::mercadona->value => self::mercadona->color(),
            self::jbla->value => self::jbla->color(),
        ];
    }
}
