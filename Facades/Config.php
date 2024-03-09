<?php

namespace Horizom\Core\Facades;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static array all()
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
    public static function getFacadeAccessor()
    {
        return \Horizom\Core\Config::class;
    }
}
