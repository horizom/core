<?php

use Horizom\Core\Facades\Response;
use Horizom\Core\Facades\View;
use Illuminate\Support\Facades\Hash;

if (!function_exists('app')) {
    /**
     * Application instance
     *
     * @return \Horizom\Core\App
     */
    function app()
    {
        return \Horizom\Core\App::getInstance();
    }
}

if (!function_exists('config')) {
    /**
     * Accessing Configuration Values
     */
    function config(string $key, $default = null)
    {
        return \Horizom\Core\Facades\Config::get($key, $default);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a URL.
     */
    function url(string $path = null)
    {
        $baseUrl = defined('HORIZOM_BASE_URL') ? trim(HORIZOM_BASE_URL, '/') : '';
        return ($path) ? $baseUrl . '/' . trim($path, '/') : $baseUrl;
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL for an asset using the current scheme of the request (HTTP or HTTPS).
     */
    function asset(string $path = null)
    {
        $baseUrl = defined('HORIZOM_BASE_URL') ? trim(HORIZOM_BASE_URL, '/') : '';
        return ($path) ? $baseUrl . '/' . $path : $baseUrl;
    }
}

if (!function_exists('view')) {
    /**
     * Return a view as the response's content
     */
    function view(string $name, array $data = [], $contentType = 'text/html')
    {
        $content = View::make($name, $data)->render();
        return Response::withHeader('Content-type', $contentType)->getBody()->write($content);
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

if (!function_exists('base_path')) {
    /**
     * Get base path helper
     *
     * @param string $path
     * @return string
     */
    function base_path(string $path = '')
    {
        return $path ? HORIZOM_ROOT . '/' . $path : HORIZOM_ROOT;
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the path to the storage folder
     *
     * @param string $path
     * @return string
     */
    function storage_path(string $path = '')
    {
        $base = base_path(path: 'storage');
        return $path ? $base . '/' . $path : $base;
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the path to the public folder
     *
     * @param string $path
     * @return string
     */
    function public_path(string $path = '')
    {
        $base = base_path('public');
        return $path ? $base . '/' . $path : $base;
    }
}

if (!function_exists('resources_path')) {
    /**
     * Get the path to the resources folder
     *
     * @param string $path
     * @return string
     */
    function resources_path(string $path = '')
    {
        $base = HORIZOM_ROOT . '/resources';
        return $path ? $base . '/' . $path : $base;
    }
}
