<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

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
        // Everything strict, all the time.
        // Model::shouldBeStrict(!$this->app->isProduction());
        Model::unguard();


        // In production, merely log lazy loading violations.
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);

                Log::warning("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        // Log a warning if query spend more than a total of 1500ms querying.
        DB::whenQueryingForLongerThan(15000, function (Connection $connection) {
            Log::warning("Database queries exceeded 15 seconds on {$connection->getName()}");
        });

        DB::listen(function ($query) {
            if ($query->time > 1000) {
                Log::warning("An individual database query exceeded 1 second.", [
                    'sql' => $query->sql
                ]);
            }
        });
    }
}
