<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'admin@tapptu.com',
            'password' => bcrypt('secret'),
            'role' => 'admin',
        ]);
        User::factory(30)->create();

        Setting::create([
            'wa_number' => '8801709090909',
            'wa_instanceid' => '16821',
            'wa_accesstoken' => 'el4WVLGaUCzKKUtfXhoBZCx7TcEFCZRPuUl0GrRD2532c431',
            'payment_public' => 'xnd_public_development_VwYmPK7P988Sq57_vaZxCPxwfcQD1V34dSP5dEu3fJv3dNHVRQjynYew7QvIHSHI',
            'payment_secret' => 'xnd_development_GIGOtNN7tzlPgDqzajyF7nDDYtl2JWgOeIsG0wb8QbivXJdgVKbUQNklAvqajC',
            'payment_webhook_secret' => 'whsec_HBBXhXPZDNnar2tdiz0TlFpER5gFBp6i',
        ]);

        Package::create([
            'name' => 'standard',
            'type' => 'month',
            'first_price' => 150000,
            'second_price' => 180000,
        ]);

        // Package::create([
        //     'name' => 'standard',
        //     'type' => 'year',
        //     'first_price' => 1.30,
        //     'second_price' => 1.50,
        // ]);

        Package::create([
            'name' => 'premium',
            'type' => 'month',
            'first_price' => 2000,
            'second_price' => 2200,
        ]);

        // Package::create([
        //     'name' => 'premium',
        //     'type' => 'year',
        //     'first_price' => 1.00,
        //     'second_price' => 1.20,
        // ]);
    }
}
