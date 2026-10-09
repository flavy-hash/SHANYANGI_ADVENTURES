<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Admin users are not seeded (no default passwords): create one with
     *   php artisan make:filament-user
     */
    public function run(): void
    {
        $this->call(ContentSeeder::class);
    }
}
