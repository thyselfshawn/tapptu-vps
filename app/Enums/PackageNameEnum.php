<?php

namespace App\Enums;

enum PackageNameEnum: string
{
    case standard = 'standard';
    case premium = 'premium';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
