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
        $this->app->bind(Convirza::class, function () {
            return new Convirza(config('convirza.apikey'));
        });

        $this->publishes([
            __DIR__.'/../config/convirza.php' => config_path('convirza.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('convirza', function () {
            return $this->app->make(Convirza::class);
        });

        $this->mergeConfigFrom(__DIR__.'/../config/convirza.php', 'convirza');
    }
}
