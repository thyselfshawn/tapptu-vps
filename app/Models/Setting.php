<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'fb_app_id',
        'wa_number',
        'wa_instanceid',
        'wa_accesstoken',
        'payment_public',
        'payment_secret',
        'payment_webhook_secret',
    ];

    // If you want to add any additional methods or relationships, you can do so here
}
