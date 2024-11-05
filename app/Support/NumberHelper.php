<?php

declare(strict_types=1);

namespace App\Support;

final class NumberHelper
{
    public static function floatToInt(float $floatNumber, int $precision = 2): int
    {
        return (int) self::round($floatNumber * pow(10, $precision));
    }

    public static function intToFloat(int $intNumber, int $precision = 4, int $displayPrecision = 2): float
    {
        $floatNumber = $intNumber / pow(10, $precision);
        return round($floatNumber, $displayPrecision);
    }

    private static function round(int|float $number, int $precision = 2): float
    {
        return round($number, $precision);
    }
}
