<?php

namespace Horizom\Core;

use Horizom\Core\Middlewares\ErrorHandlingMiddleware;
use Horizom\Dispatcher\Dispatcher;
use Horizom\Dispatcher\MiddlewareResolver;
use Horizom\Http\Request;
use Horizom\Routing\RouteCollector;
use Horizom\Routing\RouteCollectorFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;

class App
{
    /**
     * @const string Horizom Framework Version
     */
    protected const VERSION = '3.1.0';

    /**
     * @var array
     */
    protected static $settings = [
        'app.name' => 'Horizom',

        'app.env' => 'development',

        'app.base_path' => '',

        'app.base_url' => 'http://localhost:8000',

        'app.asset_url' => null,

        'app.timezone' => 'UTC',

        'app.locale' => 'en_US',

        'app.debug' => true,
    ];

    /**
     * @var string
     */
    protected $defaultNamespace;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var Container
     */
    private static $container;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var ErrorHandlerInterface
     */
    private $errorHandler;

    /**
     * @var RouteCollector
     */
    public $router;

    /**
     * @var App
     */
    private static $_instance;

    /**
     * Create new application
     */
    public function __construct(string $basePath = '', ContainerInterface $container = null)
    {
        define("HORIZOM_VERSION", self::VERSION);

        $this->basePath = $basePath;

        if ($container === null) {
            $container = new Container();
            $container->set("version", $this->version());
            $container->set(Request::class, Request::create());
        }

        $resolver = new MiddlewareResolver($container);

        $this->dispatcher = new Dispatcher([], $resolver);
        $this->router = (new RouteCollectorFactory())->create($container);

        self::$container = $container;
        self::$_instance = $this;
    }

    /**
     * Retourne l'instance de la class
     * 
     * @return Self
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Self();
        }

        return self::$_instance;
    }

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function version()
    {
        return 'Horizom (' . self::VERSION . ') PHP (' . PHP_VERSION . ')';
    }

    /**
     * Set or Get Configuration Values into the application.
     */
    public static function config(array $config = null)
    {
        if ($config !== null) {
            self::$settings = array_merge(self::$settings, $config);
            return null;
        }

        return self::$settings;
    }

    /**
     * Dependency Injection Container.
     */
    public function container(): Container
    {
        return self::$container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     */
    public function get(string $id)
    {
        return self::$container->get($id);
    }

    /**
     * Load a configuration file into the application.
     */
    public function configure(string $name): self
    {
        $config = require HORIZOM_ROOT . '/config/' . $name . '.php';
        $this->config($config);

        return $this;
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
     * Set error handler middleware
     * 
     * @param ErrorHandlerInterface|string $errorHandler
     */
    public function setErrorHandler($errorHandler): self
    {
        if (is_string($errorHandler)) {
            $this->errorHandler = new $errorHandler();
        } else {
            $this->errorHandler = $errorHandler;
        }

        return $this;
    }

    /**
     * Register a new middleware in stack
     * 
     * @param MiddlewareInterface|string|callable $middleware
     * @return self
     */
    public function add($middleware): self
    {
        $this->dispatcher->add($middleware);
        return $this;
    }

    /**
     * Run The Application
     */
    public function run()
    {
        $request = self::$container->get(\Horizom\Http\Request::class);

        if (config('app.debug') === true) {
            $accepts = $request->getHeader('Accept');
            $whoops = new \Whoops\Run();

            if (
                !empty($accepts) && $accepts[0] === 'application/json' ||
                \Whoops\Util\Misc::isAjaxRequest()
            ) {
                $whoops->pushHandler(new \Whoops\Handler\JsonResponseHandler());
            } else {
                $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
            }

            $this->add(new \Middlewares\Whoops($whoops));
        } else if ($this->errorHandler !== null) {
            $this->add(new ErrorHandlingMiddleware($this->errorHandler));
        }

        $this->add($this->router->getRouter());
        $response = $this->dispatcher->dispatch($request);

        $this->emit($response);
    }

    private function emit(ResponseInterface $response)
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
