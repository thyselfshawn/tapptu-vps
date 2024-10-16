<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Voucher;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'pagecolor',
        'fontcolor',
        'voucher',
        'instaurl',
        'googleplaceid',
        'googlereviewstart',
        'status',
        'notification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cards()
    {
        return $this->belongsToMany(Card::class, 'venue_cards', 'venue_id', 'card_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($venue) {
            $venue->slug = static::generateSlug($venue->name);
        });

        static::updating(function ($venue) {
            $venue->slug = static::generateSlug($venue->name);
        });
    }

    public static function generateSlug($name)
    {
        return str()->slug($name, '-');
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription()
    {
        return $this->subscriptions()->active()->latest()->first();
    }
}
