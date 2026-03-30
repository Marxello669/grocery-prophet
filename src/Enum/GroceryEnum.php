<?php

namespace App\Enum;

enum GroceryEnum: string
{
    // Frescos
    case meat = 'meat';
    case fish = 'fish';
    case fruit = 'fruit';
    case vegetables = 'vegetables';
    case dairy = 'dairy';
    case bakery_pastry = 'bakery_pastry';

    // Mercearia
    case rice_pasta_flour = "rice_pasta_flour";
    case canned_goods = "canned_goods";
    case breakfast_coffee = "breakfast_coffee";
    case condiments_oils = "condiments_oils";
    case snacks_sweets = "snacks_sweets";
    case bio_healthy = "bio_healthy";

    // Bebidas
    case beverage = 'beverage';
    case alcohol = 'alcohol';

    // Congelados
    case frozen = "frozen";

    // Casa e Pessoal
    case cleaning = "cleaning";
    case personal_care = "personal_care";
    case baby = "baby";
    case pets = "pets";
    case home_bazar = "home_bazar";

    public function label(): string
    {
        return match ($this) {
            self::meat => "Talho e Aves",
            self::fish => "Peixaria e Marisco",
            self::fruit => "Fruta",
            self::vegetables => "Legumes",
            self::dairy => "Laticínios e Ovos",
            self::bakery_pastry => "Padaria e Pastelaria",
            self::rice_pasta_flour => "Arroz, Massa e Farinha",
            self::canned_goods => "Conservas e Enlatados",
            self::breakfast_coffee => "Pequeno-almoço e Café",
            self::condiments_oils => "Temperos e Óleos",
            self::snacks_sweets => "Snacks, Bolachas e Doces",
            self::bio_healthy => "Bio e Vida Saudável",
            self::beverage => "Águas, Sumos e Refrigerantes",
            self::alcohol => "Garrafeira (Vinhos e Cervejas)",
            self::frozen => "Congelados",
            self::cleaning => "Limpeza da Casa",
            self::personal_care => "Higiene e Beleza",
            self::baby => "Bebé e Criança",
            self::pets => "Animais de Estimação",
            self::home_bazar => "Casa, Cozinha e Lazer",
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::meat => "🥩",
            self::fish => "🐟",
            self::fruit => "🍎",
            self::vegetables => "🥦",
            self::dairy => "🥛",
            self::bakery_pastry => "🥖",
            self::rice_pasta_flour => "🍝",
            self::canned_goods => "🥫",
            self::breakfast_coffee => "☕",
            self::condiments_oils => "🧂",
            self::snacks_sweets => "🍪",
            self::bio_healthy => "🌿",
            self::beverage => "🥤",
            self::alcohol => "🍷",
            self::frozen => "❄️",
            self::cleaning => "🧹",
            self::personal_care => "🧴",
            self::baby => "👶",
            self::pets => "🐾",
            self::home_bazar => "🏠",
        };
    }
}
