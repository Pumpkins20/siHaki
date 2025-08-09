<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\Types;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Helpers\StatusHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // ✅ ADDED: Use custom pagination view globally
        Paginator::defaultView('custom.pagination');
        Paginator::defaultSimpleView('custom.simple-pagination');

        // Cek jika DBAL aktif & enum belum diregister
        if (class_exists(\Doctrine\DBAL\Types\Type::class)) {
            /** @var \Illuminate\Database\Connection $connection */
            $connection = Schema::getConnection();
            $platform = $connection->getDoctrineSchemaManager()->getDatabasePlatform();

            // Mapping 'enum' ke 'string'
            $platform->registerDoctrineTypeMapping('enum', 'string');
        }

        // Make StatusHelper available in all views
        View::share('StatusHelper', StatusHelper::class);
    }
}
