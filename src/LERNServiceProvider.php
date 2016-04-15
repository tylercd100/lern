<?php

namespace Tylercd100\LERN;

use Illuminate\Support\ServiceProvider;

class LERNServiceProvider extends ServiceProvider
{
    public function register() {
        $this->mergeConfigFrom(__DIR__.'/../config/lern.php', 'lern');

        $this->handleDeprecatedConfigValues();

        $this->app->singleton('lern', function() {
            return new LERN;
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../migrations/2016_03_17_000000_create_lern_tables.php' => base_path('database/migrations/2016_03_17_000000_create_lern_tables.php'),
            __DIR__.'/../migrations/2016_03_27_000000_add_user_data_and_url_to_lern_tables.php' => base_path('database/migrations/2016_03_27_000000_add_user_data_and_url_to_lern_tables.php'),
            __DIR__.'/../config/lern.php' => base_path('config/lern.php'),
        ]);   
    }

    protected function handleDeprecatedConfigValues()
    {
        $renamedConfigValues = [
            [
                'oldName' => 'lern.notify.pushover.user',
                'newName' => 'lern.notify.pushover.users',
            ],
        ];

        foreach ($renamedConfigValues as $renamedConfigValue) {
            if (config($renamedConfigValue['oldName'])) {
                config([$renamedConfigValue['newName'] => config($renamedConfigValue['oldName'])]);
            }
        }
    }
}