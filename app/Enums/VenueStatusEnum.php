<?php

namespace App\Enums;

enum VenueStatusEnum: string
{
    case pending = 'pending';
    case trial = 'trial';
    case paid = 'paid';
    case expired = 'expired';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
