<?php

namespace Skidaatl\Convirza;

use Illuminate\Support\ServiceProvider;

class ConvirzaServiceProvider extends ServiceProvider
{

    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/convirza.php' => config_path('convirza.php')
        ]);

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/convirza.php', 'convirza');

        $this->app->bind('convirza', function () {
            return new Convirza(config('convirza'));
        });

        $this->app->alias('convirza', Convirza::class);
    }
}
