<?php

namespace Horizom\Core\Facades;

/**
 * @method static string render(string $view, array $data = [], array $mergeData = [])
 * @method static void component(string $class, string $alias = null, string $prefix = '')
 * @method static void directive(string $name, callable $handler)
 * @method static void if(string $name, callable $callback)
 * @method static bool exists(string $view)
 * @method static mixed share(array|string $key, mixed|null $value = null)
 * @method static array composer(array|string $views, \Closure|string $callback)
 * @method static array creator(array|string $views, \Closure|string $callback)
 * @method static \Illuminate\Contracts\View\View make(string $view, array $data = [], array $mergeData = [])
 * @method static \Illuminate\Contracts\View\View file(string $path, \Illuminate\Contracts\Support\Arrayable|array|null $data = [], array|null $mergeData = [])
 * @method static \Illuminate\Contracts\View\View addNamespace(string $namespace, array|string $hints)
 * @method static \Illuminate\Contracts\View\View replaceNamespace(string $namespace, array|string $hints)
 * @method static \Illuminate\Contracts\View\View first(array $views, \Illuminate\Contracts\Support\Arrayable|array $data = [], array $mergeData = [])
 *
 * @see \Horizom\View\View
 */
class View extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Horizom\Core\View::class;
    }
}
