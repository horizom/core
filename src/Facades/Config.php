<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static array<string, mixed> all()
 * @method static bool has($key)
 * @method static void push($key, $value)
 * @method static void put(mixed $key, mixed $value = null)
 * @method static void set(string $key, mixed $value = null)
 *
 * @see \Horizom\Core\Config
 */
class Config extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return string
     */
    public static function getFacadeAccessor(): string
    {
        return \Horizom\Core\Config::class;
    }
}
