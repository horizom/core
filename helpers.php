<?php

use Horizom\Core\App;
use Horizom\Core\View;
use Illuminate\Support\Facades\Hash;
use Nyholm\Psr7\Factory\Psr17Factory;

if (!function_exists('app')) {
    /**
     * Application
     */
    function app()
    {
        return App::getInstance();
    }
}

if (!function_exists('config')) {
    /**
     * Accessing Configuration Values
     */
    function config(string $key, $default = null)
    {
        $configs = App::config();
        return isset($configs[$key]) ? $configs[$key] : $default;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL.
     */
    function url(string $path = null)
    {
        $base_url = trim(HORIZOM_BASE_URL, '/');
        return ($path) ? $base_url . '/' . trim($path, '/') : $base_url;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for an asset using the current scheme of the request (HTTP or HTTPS).
     */
    function asset(string $path = null)
    {
        $base_url = trim(HORIZOM_BASE_URL, '/');
        return ($path) ? $base_url . '/' . $path : $base_url;
    }
}

if (!function_exists('view')) {
    /**
     * Return a view as the response's content
     */
    function view(string $name, array $data = [], $contentType = 'text/html')
    {
        $factory = new Psr17Factory();
        $content = (new View())->make($name, $data)->render();
        $body = $factory->createStream($content);

        return $factory->createResponse()->withHeader('Content-type', $contentType)->withBody($body);
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Hashes the given value using Bcrypt.
     */
    function bcrypt(string $value)
    {
        return Hash::make($value);
    }
}

if (!function_exists('debug')) {
    /**
     * var_dump & die
     */
    function debug($var)
    {
        var_dump($var);
        die;
    }
}
