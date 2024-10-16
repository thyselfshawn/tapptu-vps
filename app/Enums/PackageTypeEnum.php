<?php

namespace App\Enums;

enum PackageTypeEnum: string
{
    case monthly = 'monthly';
    case yearly = 'yearly';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
