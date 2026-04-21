<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

use Horizom\Core\Facades\Facade;

/**
 * @method static \Horizom\Http\Response create()
 * @method static mixed post(string $name = null, $default = null)
 * @method static mixed query(string $name = null, $default = null)
 * @method static mixed files(string $name = null)
 * @method static mixed cookie(string $name = null, $default = null)
 * @method static mixed server(string $name = null, $default = null)
 * @method static \Illuminate\Support\Collection collect()
 * @method static array all()
 * @method static mixed input($key = null, $default = null)
 * @method static bool has($key)
 * @method static bool missing($key)
 * @method static \Illuminate\Support\Collection only($keys)
 * @method static \Illuminate\Support\Collection except($keys)
 * @method static string string(string $key = null, $default = null)
 * @method static int integer(string $key = null, $default = null)
 * @method static float float(string $key = null, $default = null)
 * @method static bool boolean(string $key = null, $default = null)
 * @method static \Illuminate\Support\Collection merge(array $input)
 * @method static bool hasFile(string $key)
 * @method static string path()
 * @method static string method()
 * @method static bool isMethod(string $method)
 * @method static mixed header(string $key, $default = null)
 * @method static array headers()
 * @method static string|null bearerToken()
 * @method static string root()
 * @method static string url()
 * @method static string fullUrl()
 * @method static bool is(...$patterns)
 * @method static string baseUrl()
 * @method static string basePath()
 * @method static array userAgent()
 * @method static mixed route($param = null, $default = null)
 * @method static string fingerprint()
 * @method static string ip()
 * @method static bool secure()
 * @method static bool isSecure()
 * @method static bool ajax()
 * @method static bool pjax()
 * @method static bool isXmlHttpRequest()
 * @method static \Closure getRouteResolver()
 * @method static Psr\Http\Message\UriInterface getUriFromGlobals()
 *
 * @see \Horizom\Http\Request
 */
class Request extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Horizom\Http\Request::class;
    }
}
