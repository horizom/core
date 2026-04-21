<?php

declare(strict_types=1);

namespace Horizom\Core;

abstract class ServiceProvider
{
    /**
     * @var App
     */
    protected App $app;

    /**
     * Create a new service provider instance.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // no-op
    }

    /**
     * Binds and sets up implementations at boot time.
     */
    public function boot(): void
    {
        // no-op
    }

    /**
     * Get the services provided by the provider.
     *
     * @return list<string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return list<string>
     */
    public function when(): array
    {
        return [];
    }

    /**
     * @return \Illuminate\Support\Collection<int, class-string<ServiceProvider>>
     */
    public static function defaultProviders(): \Illuminate\Support\Collection
    {
        return collect([
            \Horizom\Core\Providers\CoreServiceProvider::class,
            \Horizom\Core\Providers\ExceptionServiceProvider::class,
            \Horizom\Core\Providers\ViewServiceProvider::class,
        ]);
    }
}
