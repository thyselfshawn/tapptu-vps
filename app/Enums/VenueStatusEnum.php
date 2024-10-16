<?php

namespace App\Enums;

enum VenueStatusEnum: string
{
    case pending = 'pending';
    case online = 'online';
    case offline = 'offline';
    case canceled = 'canceled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
