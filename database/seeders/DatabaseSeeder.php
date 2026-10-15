<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan Seeder Landing Page biar gak error property on null
        $this->call([
            LandingPageSeeder::class,
            AkunSeeder::class,
        ]);

        // Seed User Admin
        User::updateOrCreate(
            ['email' => 'pemilik@gmail.com'],
            [
                'name' => 'pemilik',
                'password' => bcrypt('pemilik.12345'),
                'role' => 'pemilik',
                'email_verified_at' => now(),
            ]
        );
    }
} 