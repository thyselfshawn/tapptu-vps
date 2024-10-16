<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\CardStatusEnum;

class Card extends Model
{
    use HasFactory;

    protected $table = 'cards';

    protected $fillable = [
        'uuid',
        'name',
        'token',
        'status',
    ];

    protected $casts = [
        'status' => CardStatusEnum::class,
    ];

    // Define the relationship with the Venue model
    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venue_cards', 'card_id', 'venue_id');
    }

    public function firstVenue()
    {
        return $this->venues()->first();
    }
}
