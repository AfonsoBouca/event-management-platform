<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Runs the application schema and seed data script.
     */
    public function run(): void
    {
        // Get the schema name from the environment.
        $schema = env('DB_SCHEMA');

        // Load the raw SQL file
        $path = base_path('database/event-management.sql');
        $sql = file_get_contents($path);

        // Expose the configured schema to the SQL script.
        if ($schema !== null) {
            DB::statement("SELECT set_config('app.schema', ?, false)", [$schema]);
        }

        // Run the SQL script
        DB::unprepared($sql);

        // Show a message in the Artisan console
        $this->command?->info('Database seeded using schema: ' . ($schema ?? 'event_platform (default)'));
    }
}
