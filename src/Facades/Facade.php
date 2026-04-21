<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

use Horizom\Core\Contracts\FacadeContract;

abstract class Facade implements FacadeContract
{
    /**
     * Get facade accessor
     */
    abstract public static function getFacadeAccessor(): string;

    /**
     * Get the root object behind the facade.
     */
    public static function getFacadeRoot(): mixed
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Resolve the facade root instance from the container.
     */
    protected static function resolveFacadeInstance(string $name): mixed
    {
        return app()->get($name);
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array<int, mixed>  $args
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new \RuntimeException('A facade root has not been set.');
        }

        return $instance->$method(...$args);
    }
}
