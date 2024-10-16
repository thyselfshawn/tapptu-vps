<?php

namespace App\Enums;

enum CardStatusEnum: string
{
    case pending = 'pending';
    case attached = 'attached';
    case paused = 'paused';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
