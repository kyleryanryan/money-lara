<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\UserSeeder;

class SeedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:users {count=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a specified number of users into the database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        $this->info("Seeding {$count} users...");

        $seeder = new UserSeeder();
        $seeder->run($count);

        $this->info('User seeding completed.');
    }
}
