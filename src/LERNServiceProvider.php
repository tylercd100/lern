<?php

namespace Tylercd100\LERN;

use Illuminate\Support\ServiceProvider;

class LERNServiceProvider extends ServiceProvider
{
    public function register() {
        $this->mergeConfigFrom(__DIR__ . '/../config/lern.php', 'lern');

        $this->app->singleton('lern', function() {
            return new LERN;
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../migrations/2016_03_17_000000_create_lern_tables.php' => base_path('database/migrations/2016_03_17_000000_create_lern_tables.php'),
            __DIR__ . '/../config/lern.php' => base_path('config/lern.php'),
        ]);   
    }
}