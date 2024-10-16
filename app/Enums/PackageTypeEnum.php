<?php

namespace App\Enums;

enum PackageTypeEnum: string
{
    case month = 'month';
    case year = 'year';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
