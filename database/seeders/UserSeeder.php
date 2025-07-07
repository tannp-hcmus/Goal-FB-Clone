<?php

namespace Database\Seeders;

use App\Infrastructure\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Log::info('Starting to seed 3000 users...');

        // Disable mass assignment protection for seeding
        User::unguard();

        // Use chunking to avoid memory issues when creating large amounts of data
        $chunkSize = 100;
        $totalUsers = 3000;
        $chunks = ceil($totalUsers / $chunkSize);

        DB::transaction(function () use ($chunkSize, $chunks, $totalUsers) {
            for ($i = 0; $i < $chunks; $i++) {
                $usersToCreate = min($chunkSize, $totalUsers - ($i * $chunkSize));

                User::factory()
                    ->count($usersToCreate)
                    ->create();

                Log::info("Created chunk " . ($i + 1) . " of {$chunks} - {$usersToCreate} users");
            }
        });

        // Re-enable mass assignment protection
        User::reguard();

        Log::info('Successfully seeded 3000 users for Elasticsearch indexing');

        $this->command->info('🎯 Created 3000 sample users ready for Elasticsearch indexing!');
        $this->command->info('💡 Run "php artisan user:index" to index these users into Elasticsearch');
    }
}
