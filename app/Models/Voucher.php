<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Card;
use App\Enums\VoucherStatusEnum;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'card_id',
        'venue_id',
        'uuid',
        'text',
        'status',
        'claimed_at',
    ];

    protected $casts = [
        'status' => VoucherStatusEnum::class,
    ];

    /**
     * Get the venue that owns the voucher.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }    
}

