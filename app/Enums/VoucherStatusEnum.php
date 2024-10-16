<?php

namespace App\Enums;

enum VoucherStatusEnum: string
{
    case pending = 'pending';
    case mistake = 'mistake';
    case claimed = 'claimed';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
