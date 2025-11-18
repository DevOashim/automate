<?php

namespace DevOashim\Automate;

use Illuminate\Support\ServiceProvider;

class AutomateServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Config merge
        $this->mergeConfigFrom(
            __DIR__.'/../config/automate.php', 'automate'
        );
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'auto');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/automate.php' => config_path('automate.php'),
        ], 'automate-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../views' => resource_path('views/vendor/automate'),
        ], 'automate-views');
    }
}
