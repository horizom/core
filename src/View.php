<?php

declare(strict_types=1);

namespace Horizom\Core;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\Container as ContainerInterface;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider;

class View implements FactoryContract
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var BladeCompiler
     */
    private $compiler;

    /**
     * @param array<int, string> $viewPaths
     */
    public function __construct(array $viewPaths, string $cachePath, ContainerInterface $container = null)
    {
        $this->container = $container ?: new Container;

        $this->setupContainer($viewPaths, $cachePath);
        (new ViewServiceProvider($this->container))->register();

        $this->factory = $this->container->get('view');
        $this->compiler = $this->container->get('blade.compiler');
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @param string $view
     * @param array<string, mixed> $data
     * @param array<string, mixed> $mergeData
     * @return string
     */
    public function render(string $view, array $data = [], array $mergeData = [])
    {
        return $this->make($view, $data, $mergeData)->render();
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $view
     * @param array<string, mixed> $data
     * @param array<string, mixed> $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        return $this->factory->make($view, $data, $mergeData);
    }

    /**
     * Get the view compiler.
     *
     * @return BladeCompiler
     */
    public function compiler()
    {
        return $this->compiler;
    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function directive(string $name, callable $handler): void
    {
        $this->compiler->directive($name, $handler);
    }

    /**
     * @param string $class
     * @param string|null $alias
     * @param string $prefix
     */
    public function component(string $class, string $alias = null, string $prefix = ''): void
    {
        $this->compiler->component($class, $alias, $prefix);
    }

    /**
     * @param string $name
     * @param callable $callback
     */
    public function if(string $name, callable $callback): void
    {
        $this->compiler->if($name, $callback);
    }

    /**
     * Determine if a given view exists.
     *
     * @param string $view
     * @return bool
     */
    public function exists($view)
    {
        return $this->factory->exists($view);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param string $path
     * @param array<string, mixed> $data
     * @param array<string, mixed> $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function file($path, $data = [], $mergeData = []): \Illuminate\Contracts\View\View
    {
        return $this->factory->file($path, $data, $mergeData);
    }

    /**
     * Add a piece of shared data to the environment.
     *
     * @param array<string, mixed>|string $key
     * @param mixed $value
     * @return mixed
     */
    public function share($key, $value = null)
    {
        return $this->factory->share($key, $value);
    }

    /**
     * Register a view composer event
     *
     * @param array<int, string>|string $views
     * @param \Closure|string $callback
     * @return array<int, mixed>
     */
    public function composer($views, $callback): array
    {
        return $this->factory->composer($views, $callback instanceof \Closure || is_string($callback) ? $callback : \Closure::fromCallable($callback));
    }

    /**
     * Register a view creator event.
     *
     * @param array<int, string>|string $views
     * @param \Closure|string $callback
     * @return array<int, mixed>
     */
    public function creator($views, $callback): array
    {
        return $this->factory->creator($views, $callback instanceof \Closure || is_string($callback) ? $callback : \Closure::fromCallable($callback));
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param array<string, string> $hints
     * @return static
     */
    public function addNamespace($namespace, $hints)
    {
        $this->factory->addNamespace($namespace, $hints);

        return $this;
    }

    /**
     * Replace the namespace hints for the given namespace.
     *
     * @param string $namespace
     * @param array<int, string>|string $hints
     */
    public function replaceNamespace($namespace, $hints): self
    {
        $this->factory->replaceNamespace($namespace, $hints);

        return $this;
    }

    /**
     * @param array<int, mixed> $params
     */
    public function __call(string $method, array $params): mixed
    {
        return $this->factory->$method(...$params);
    }

    /**
     * @param array<int, string> $viewPaths
     */
    protected function setupContainer(array $viewPaths, string $cachePath): void
    {
        $this->container->bindIf('files', fn() => new Filesystem(), true);
        $this->container->bindIf('events', fn() => new Dispatcher(), true);

        $this->container->bindIf('config', function () use ($viewPaths, $cachePath) {
            return ['view.paths' => $viewPaths, 'view.compiled' => $cachePath];
        }, true);

        Facade::setFacadeApplication($this->container);
    }
}
