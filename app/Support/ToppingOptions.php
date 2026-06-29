<?php

namespace App\Support;

class ToppingOptions
{
    public const PRICES = [
        'No Toppings' => 0,
        'Chocolate Flakes (+RM2)' => 2,
        'Chocolate Ball (+RM2)' => 2,
        'Kitkat Ball (+RM3)' => 3,
        'Kitkat Bar (+RM3)' => 3,
        'Kinder Bueno (+RM5)' => 5,
        'M&M (+RM3)' => 3,
        'Oreo Crunch (+RM3)' => 3,
        'Almond (+RM4)' => 4,
    ];

    public static function all(): array
    {
        return self::PRICES;
    }

    public static function surcharge(?string $topping): float
    {
        return (float) (self::PRICES[$topping] ?? 0);
    }

    public static function totalPrice(float|string|null $basePrice, ?string $topping): float
    {
        return round((float) $basePrice + self::surcharge($topping), 2);
    }
}
