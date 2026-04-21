<?php

declare(strict_types=1);

namespace Horizom\Core;

use DI\ContainerBuilder;
use Horizom\Dispatcher\Dispatcher;
use Horizom\Dispatcher\MiddlewareResolver;
use Horizom\Http\Request;
use Horizom\Routing\Router;
use Horizom\Routing\RouterFactory;
use Psr\Http\Message\ResponseInterface;

class App
{
    /**
     * @var self|null
     */
    private static $instance;

    /**
     * @const string Horizom Framework Version
     */
    protected const VERSION = '4.0.0';

    /**
     * @var string
     */
    protected $defaultNamespace = '';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var \Horizom\Core\Container
     */
    private $container;

    /**
     * @var \Horizom\Dispatcher\Dispatcher
     */
    private $dispatcher;

    /**
     * @var \Horizom\Routing\Router
     */
    public $router;

    /**
     * Create new application
     *
     * @param string $basePath
     * @param Container|null $container
     */
    public function __construct(string $basePath = '', Container $container = null)
    {
        // Load environment variables
        \Dotenv\Dotenv::createImmutable(HORIZOM_ROOT)->safeLoad();

        // Set base path
        $this->basePath = $basePath;

        if ($container === null) {
            $containerBuilder = new ContainerBuilder(Container::class);
            $built = $containerBuilder->useAutowiring(true)->build();
            assert($built instanceof Container);
            $container = $built;
        }

        $this->container = $container;
        $this->dispatcher = new Dispatcher([], new MiddlewareResolver($container));
        $this->router = (new RouterFactory)->create($container);

        $this->instance(Config::class, new Config());

        self::$instance = $this;
    }

    /**
     * Return the application instance
     *
     * @return static
     */
    public static function getInstance(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        /** @var static $instance */
        $instance = self::$instance;
        return $instance;
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version(): string
    {
        return self::VERSION;
    }

    /**
     * Set your application base path
     *
     * If you want to run your Slim Application from a sub-directory
     * in your Server’s Root instead of creating a Virtual Host
     *
     * @param string $path Path to your Application
     */
    public function setBasePath(string $path = ''): self
    {
        $this->basePath = $path;
        return $this;
    }

    /**
     * Register a new middleware in stack
     *
     * @param \Psr\Http\Server\MiddlewareInterface|\Psr\Http\Server\RequestHandlerInterface|string $middleware
     * @return self
     */
    public function add(\Psr\Http\Server\MiddlewareInterface|\Psr\Http\Server\RequestHandlerInterface|string $middleware): self
    {
        $this->dispatcher->add($middleware);
        return $this;
    }

    /**
     * Build an entry of the container by its name.
     * This method behave like get() except resolves the entry again every time.
     *
     * @template T
     * @param class-string<T> $name
     * @param array<string, mixed> $parameters
     * @return T
     */
    public function make(string $name, array $parameters = [])
    {
        return $this->container->make($name, $parameters);
    }

    /**
     * Register a shared binding in the container.
     *
     * @param string $abstract
     * @param callable $concrete
     */
    public function singleton(string $abstract, $concrete = null): void
    {
        $this->set($abstract, $concrete ?? $abstract);
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $abstract
     * @param mixed $instance
     */
    public function instance(string $abstract, mixed $instance): void
    {
        $this->set($abstract, $instance);
    }

    /**
     * Define an object in the container.
     *
     * @param string $abstract
     * @param mixed $concrete
     */
    public function bind(string $abstract, mixed $concrete): void
    {
        $this->set($abstract, $concrete);
    }

    /**
     * Check if the container can provide an entry for the given id.
     *
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * Extend an entry of the container.
     *
     * @param string $id
     * @param callable $callback
     */
    public function extend(string $id, callable $callback): void
    {
        $old = $this->container->get($id);
        $this->container->set($id, fn() => $callback($old));
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string $name
     * @param mixed $value
     */
    public function set(string $name, mixed $value): void
    {
        $this->container->set($name, $value);
    }

    /**
     * Get an entry of the container by its id.
     *
     * @template T
     * @param class-string<T> $id
     * @return T
     */
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    /**
     * Resolve a service provider instance from the class name.
     *
     * @param class-string<ServiceProvider> $provider
     */
    public function resolveProvider(string $provider): ServiceProvider
    {
        return new $provider($this);
    }

    /**
     * Register a service provider.
     *
     * @param ServiceProvider|class-string<ServiceProvider> $provider
     */
    public function register(ServiceProvider|string $provider): ServiceProvider
    {
        if (is_string($provider)) {
            $provider = $this->resolveProvider($provider);
        }

        return $this->container->register($provider);
    }

    /**
     * Boots up the application calling the `boot` method of each registered service provider.
     *
     * @see \Horizom\Core\ServiceProvider::boot()
     */
    public function boot(): void
    {
        $this->container->boot();
    }

    /**
     * Set or Get Configuration Values into the application.
     *
     * @param array<string, mixed>|null $items
     * @return array<string, mixed>|null
     */
    public function config(?array $items = null): ?array
    {
        if (is_null($items)) {
            return $this->get(Config::class)->all();
        }

        $this->updateConfig($items);
        return null;
    }

    /**
     * Load a configuration file into the application.
     */
    public function configure(string $name): self
    {
        $this->updateConfig(
            (array) require base_path("config/{$name}.php")
        );

        return $this;
    }

    /**
     * @param array<string, mixed> $items
     */
    public function updateConfig(array $items): void
    {
        $config = $this->get(Config::class);
        $items = array_merge($config->all(), $items);

        $this->instance(Config::class, Config::make($items));
    }

    /**
     * Router getter
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Run The Application
     *
     * @param \Horizom\Http\Request $request
     */
    public function run(Request $request): void
    {
        $this->instance(Request::class, $request);
        $this->instance(Router::class, $this->router);

        $this->registerServiceProvidersAndBoot();

        // Add the router middleware
        $this->add($this->router->getRouter());

        // Send the response to the browser
        $this->emit(
            $this->dispatcher->dispatch($request)
        );
    }

    /**
     * Register service providers and boot them if the application is already booted.
     *
     * @return void
     */
    protected function registerServiceProvidersAndBoot(): void
    {
        foreach (config('providers') as $provider) {
            $this->register($provider);
        }

        $this->boot();
    }

    /**
     * Send the response to the browser.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return void
     */
    public function emit(ResponseInterface $response): void
    {
        $http_line = sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        header($http_line, true, $response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header("$name: $value", false);
            }
        }

        $stream = $response->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        while (!$stream->eof()) {
            echo $stream->read(1024 * 8);
        }
    }
}
