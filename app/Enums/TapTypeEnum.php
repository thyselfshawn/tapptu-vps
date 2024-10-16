<?php

namespace App\Enums;

enum TapTypeEnum: string
{
    case google_feedback = 'google_feedback';
    case good_feedback = 'good_feedback';
    case bad_feedback = 'bad_feedback';
    case venue_page = 'venue_page';
    case voucher_sent = 'voucher_sent';
    case voucher_claim = 'voucher_claim';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
