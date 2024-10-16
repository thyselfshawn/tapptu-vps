<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Card;
use App\Models\Venue;
use App\Enums\ReviewStatusEnum;
use App\Enums\ReviewTypeEnum;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $fillable = [
        'name',
        'phone',
        'venue_id',
        'type',
        'card_id',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => ReviewStatusEnum::class,
        'type' => ReviewTypeEnum::class,
    ];

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
