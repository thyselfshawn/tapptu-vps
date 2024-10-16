<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TapTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tap extends Model
{
    use HasFactory;

    protected $table = 'taps';

    protected $fillable = [
        'card_id',
        'venue_id',
        'type',
    ];

    protected $casts = [
        'type' => TapTypeEnum::class,
    ];

    /**
     * Get the card that owns the Tap.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    /**
     * Get the venue that owns the Tap.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
