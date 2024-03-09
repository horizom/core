<?php

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
     * @var array<string, bool>
     */
    private $loadedProviders = [];

    /**
     * @var array<int, ServiceProvider>
     */
    private $serviceProviders = [];

    /**
     * Register a service provider with the application.
     *
     * @param ServiceProvider $provider
     */
    public function register($provider, $force = false)
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
     * @param  ServiceProvider|string  $provider
     * @return ServiceProvider|null
     */
    public function getProvider($provider)
    {
        return array_values($this->getProviders($provider))[0] ?? null;
    }

    /**
     * Get the registered service provider instances if any exist.
     *
     * @param  ServiceProvider|string  $provider
     * @return array
     */
    public function getProviders($provider)
    {
        $name = is_string($provider) ? $provider : get_class($provider);

        return Arr::where($this->serviceProviders, fn($value) => $value instanceof $name);
    }

    /**
     * Mark the given provider as registered.
     *
     * @param  ServiceProvider  $provider
     * @return void
     */
    protected function markAsRegistered($provider)
    {
        $this->serviceProviders[] = $provider;
        $this->loadedProviders[get_class($provider)] = true;
    }
}
