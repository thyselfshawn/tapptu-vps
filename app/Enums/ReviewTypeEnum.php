<?php

namespace App\Enums;

enum ReviewTypeEnum: string
{
    case good_feedback = 'good_feedback';
    case bad_feedback = 'bad_feedback';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
