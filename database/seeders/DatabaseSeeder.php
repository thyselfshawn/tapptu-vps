<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Package;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::factory(10)->create();

                Package::create([
            'name' => 'standard',
            'type' => 'monthly',
            'first_price' => 180,
            'second_price' => 200,
        ]);

        Package::create([
            'name' => 'standard',
            'type' => 'yearly',
            'first_price' => 150,
            'second_price' => 180,
        ]);

        Package::create([
            'name' => 'premium',
            'type' => 'monthly',
            'first_price' => 120,
            'second_price' => 150,
        ]);

        Package::create([
            'name' => 'premium',
            'type' => 'yearly',
            'first_price' => 80,
            'second_price' => 100,
        ]);
    }
}
