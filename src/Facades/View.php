<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

/**
 * @method static string render(string $view, array<string, mixed> $data = [], array<string, mixed> $mergeData = [])
 * @method static void component(string $class, string $alias = null, string $prefix = '')
 * @method static void directive(string $name, callable $handler)
 * @method static void if(string $name, callable $callback)
 * @method static bool exists(string $view)
 * @method static mixed share(array<string, mixed>|string $key, mixed $value = null)
 * @method static array<int, mixed> composer(array<int, string>|string $views, \Closure|string $callback)
 * @method static array<int, mixed> creator(array<int, string>|string $views, \Closure|string $callback)
 * @method static \Illuminate\Contracts\View\View make(string $view, array<string, mixed> $data = [], array<string, mixed> $mergeData = [])
 * @method static \Illuminate\Contracts\View\View file(string $path, array<string, mixed>|null $data = [], array<string, mixed>|null $mergeData = [])
 * @method static \Illuminate\Contracts\View\View addNamespace(string $namespace, array<int, string>|string $hints)
 * @method static \Illuminate\Contracts\View\View replaceNamespace(string $namespace, array<int, string>|string $hints)
 * @method static \Illuminate\Contracts\View\View first(array<int, string> $views, array<string, mixed> $data = [], array<string, mixed> $mergeData = [])
 *
 * @see \Horizom\Core\View
 */
class View extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return \Horizom\Core\View::class;
    }
}
