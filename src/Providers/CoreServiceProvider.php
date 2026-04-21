<?php

declare(strict_types=1);

namespace Horizom\Core\Providers;

use Horizom\Core\ServiceProvider;
use Horizom\Http\Request;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // no-op
    }

    /**
     * Binds and sets up implementations at boot time.
     */
    public function boot(): void
    {
        $request = $this->app->get(Request::class);

        if (!defined('HORIZOM_VERSION')) {
            define('HORIZOM_VERSION', $this->app->version());
        }

        if (!defined('HORIZOM_BASE_PATH')) {
            define('HORIZOM_BASE_PATH', $request->basePath());
        }

        if (!defined('HORIZOM_BASE_URL')) {
            define('HORIZOM_BASE_URL', $request->baseUrl());
        }
    }
}
