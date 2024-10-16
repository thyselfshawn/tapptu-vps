<?php

namespace App\Enums;

enum ReviewStatusEnum: string
{
    case pending = 'pending';
    case seen = 'seen';
    case replied = 'replied';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
