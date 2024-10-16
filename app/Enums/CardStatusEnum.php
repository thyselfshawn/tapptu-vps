<?php

namespace App\Enums;

enum CardStatusEnum: string
{
    case pending = 'pending';
    case setup = 'setup';
    case trial = 'trial';
    case paid = 'paid';
    case attached = 'attached';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
