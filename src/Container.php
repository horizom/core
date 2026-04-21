<?php

declare(strict_types=1);

namespace Horizom\Core;

use DI\Container as DIContainer;
use Illuminate\Support\Arr;
use Psr\Container\ContainerInterface;

/**
 * Horizom's default DI container is php-di/php-di.
 *
 * Horizom\App expects a container that implements Psr\Container\ContainerInterface
 * with these service keys configured and ready for use:
 *
 *  `version`           version number of the application.
 *  `request`           an instance of \Psr\Http\Message\ServerRequestInterface
 *  `response`          an instance of \Psr\Http\Message\ResponseInterface
 *  `callableResolver`  an instance of \Horizom\Interfaces\CallableResolverInterface
 *  `foundHandler`      an instance of \Horizom\Interfaces\InvocationStrategyInterface
 *  `errorHandler`      a callable with the signature: function($request, $response, $exception)
 *  `notFoundHandler`   a callable with the signature: function($request, $response)
 *  `notAllowedHandler` a callable with the signature: function($request, $response, $allowedHttpMethods)
 */
class Container extends DIContainer implements ContainerInterface
{
    /**
     * @var array<class-string, bool>
     */
    private array $loadedProviders = [];

    /**
     * @var array<int, ServiceProvider>
     */
    private array $serviceProviders = [];

    /**
     * Register a service provider with the application.
     */
    public function register(ServiceProvider $provider, bool $force = false): ServiceProvider
    {
        if (($registered = $this->getProvider($provider)) && !$force) {
            return $registered;
        }

        $provider->register();
        $this->markAsRegistered($provider);

        return $provider;
    }

    /**
     * Boots up the application calling the `boot` method of each registered service provider.
     *
     * @see \Horizom\Core\ServiceProvider::boot()
     */
    public function boot(): void
    {
        if (!empty($this->serviceProviders)) {
            foreach ($this->serviceProviders as $provider) {
                $provider->boot();
            }
        }
    }

    /**
     * Get the registered service provider instance if it exists.
     *
     * @param  ServiceProvider|class-string  $provider
     */
    public function getProvider(ServiceProvider|string $provider): ?ServiceProvider
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  ServiceProvider|class-string  $provider
     * @return array<int, ServiceProvider>
     */
    public function getProviders(ServiceProvider|string $provider): array
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::where($this->serviceProviders, fn($value) => $value instanceof $name);
    }

    /**
     * Check whether a provider has been loaded.
     */
    public function isLoaded(string $provider): bool
    {
        return isset($this->loadedProviders[$provider]);
    }

    /**
     * Mark the given provider as registered.
     */
    protected function markAsRegistered(ServiceProvider $provider): void
    {
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = true;
    }
}
