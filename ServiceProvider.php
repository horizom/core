<?php

declare(strict_types=1);

namespace Horizom\Core;

abstract class ServiceProvider
{
    /**
     * @var App
     */
    protected $app;

    /**
     * Create a new service provider instance.
     *
     * @param  App  $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // no-op
    }

    /**
     * Binds and sets up implementations at boot time.
     *
     * @return void The method will not return any value.
     */
    public function boot()
    {
        // no-op
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [];
    }

    public static function defaultProviders()
    {
        return collect([
            \Horizom\Core\Providers\CoreServiceProvider::class,
            \Horizom\Core\Providers\ExceptionServiceProvider::class,
            \Horizom\Core\Providers\ViewServiceProvider::class,
        ]);
    }
}
