<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardVenue extends Model
{
    use HasFactory;

    protected $table = 'venue_cards';

    protected $fillable = [
        'card_id',
        'venue_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the relationship with the Venue model
    public function venues()
    {
        return $this->belongsToMany(Venue::class, 'venue_cards', 'card_id', 'venue_id');
    }
}

