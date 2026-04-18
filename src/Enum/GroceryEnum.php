<?php

namespace App\Enum;

enum GroceryEnum: string
{
    case beverage = 'beverage';
    case pets = "pets";
    case rice_pasta_flour = "rice_pasta_flour";
    case oils_vinegar = "oils_vinegar";
    case baby = "baby";
    case bio_healthy = "bio_healthy";
    case home_bazar = "home_bazar";
    case frozen = "frozen";
    case canned_goods = "canned_goods";
    case fruit = 'fruit';
    case alcohol = 'alcohol';
    case personal_care = "personal_care";
    case dairy = 'dairy';
    case vegetables = 'vegetables';
    case cleaning = "cleaning";
    case bakery_pastry = 'bakery_pastry';
    case fish = 'fish';
    case breakfast_coffee = "breakfast_coffee";
    case meal = "meal";
    case snacks_sweets = "snacks_sweets";
    case meat = 'meat';
    case condiments = "condiments";

    public function label(): string
    {
        return match ($this) {
            self::beverage => "Águas, Sumos e Refrigerantes",
            self::pets => "Animais de Estimação",
            self::rice_pasta_flour => "Arroz, Massa e Farinha",
            self::oils_vinegar => "Azeites, Óleos e Vinagre",
            self::baby => "Bebé e Criança",
            self::bio_healthy => "Bio e Vida Saudável",
            self::home_bazar => "Casa, Cozinha e Lazer",
            self::frozen => "Congelados",
            self::canned_goods => "Conservas e Enlatados",
            self::fruit => "Fruta",
            self::alcohol => "Garrafeira (Vinhos e Cervejas)",
            self::personal_care => "Higiene e Beleza",
            self::dairy => "Laticínios e Ovos",
            self::vegetables => "Legumes",
            self::cleaning => "Limpeza da Casa",
            self::bakery_pastry => "Padaria e Pastelaria",
            self::fish => "Peixaria e Marisco",
            self::breakfast_coffee => "Pequeno-almoço e Café",
            self::meal => "Refeições",
            self::snacks_sweets => "Snacks, Bolachas e Doces",
            self::meat => "Talho e Aves",
            self::condiments => "Temperos e Especiarias",
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
            self::condiments => "🧂",
            self::oils_vinegar => "🫒",
            self::meal => "🍽️",
        };
    }
}
