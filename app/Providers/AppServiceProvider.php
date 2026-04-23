<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable mass-assignment protection globally (needed by Filament)
        Model::unguard();

        // Enable automatic eager loading of accessed relationships to reduce N+1 queries
        Model::automaticallyEagerLoadRelationships();

        // Enforce stricter model behavior in non-production (helps catch errors early)
        Model::shouldBeStrict(! $this->app->isProduction());

        // Block DROP/TRUNCATE/DELETE without WHERE in production for safety
        DB::prohibitDestructiveCommands($this->app->isProduction());

        // Force HTTPS in production
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Set Carbon default locale
        Carbon::setLocale($this->app->getLocale());
    }
}
