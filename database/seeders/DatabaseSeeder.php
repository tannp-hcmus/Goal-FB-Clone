<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the UserSeeder to create 3000 sample users
        $this->call([
            UserSeeder::class,
        ]);
    }
}
