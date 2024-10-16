<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Venue;
use App\Models\Package;

class Membership extends Model
{
    protected $fillable = [
        'venue_id',
        'package_id',
        'status',
        'end_at',
        'stripe_subscription_id',
    ];

    // Add this to cast your dates correctly
    protected $casts = [
        'created_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($membership) {
            $package = $membership->package;

            // Check the package type and set the end_at accordingly
            if ($package->type === 'monthly') {
                $membership->end_at = Carbon::now()->addMonth();
            } elseif ($package->type === 'yearly') {
                $membership->end_at = Carbon::now()->addYear();
            }
        });
    }

    public function venue()
    {
        return $this->belongsTo(Venue::class);
    }

    // Define the relationship to Package
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function isEndingSoon($days = 7)
    {
        // Ensure end_at is a valid Carbon instance
        if (!$this->end_at instanceof \Carbon\Carbon) {
            return false;
        }

        // Calculate the absolute difference in days (ignores past/future direction)
        $diffInDays = abs($this->end_at->diffInDays(now(), false));

        // Return true only if end_at is within the specified number of days and in the future
        return $this->end_at->isFuture() && $diffInDays <= $days;
    }



    public function daysUntilEnds()
    {
        return $this->end_at->diffInDays(now());
    }
    
    public function timeUntilEnds()
    {
        return $this->end_at->diffForHumans([
            'parts' => 2, // Show both days and hours
            'short' => true, // Shorten the format (e.g., "1d 3h")
            'join' => true, // Join the parts with a space
        ]);
    }

    public function membershipType()
    {
        return $this->package->name;
    }
}
