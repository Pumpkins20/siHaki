<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupDevelopment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup development environment for HKI system';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up HKI System Development Environment...');

        // Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate:fresh');

        // Run seeders
        $this->info('Running seeders...');
        Artisan::call('db:seed');

        // Create storage link
        $this->info('Creating storage link...');
        Artisan::call('storage:link');

        // Clear cache
        $this->info('Clearing cache...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        $this->info('✅ Development environment setup complete!');
        $this->info('');
        $this->info('Default login credentials:');
        $this->info('Admin: admin@amikom.ac.id / password123');
        $this->info('Reviewer: reviewer1@amikom.ac.id / password123');
        $this->info('User: user1@amikom.ac.id / password123');

        return Command::SUCCESS;
    }
}
