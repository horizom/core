<?php

namespace Horizom\Core\Providers;

use Horizom\Core\ServiceProvider;
use Horizom\Http\Request;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // no-op
    }

    /**
     * Binds and sets up implementations at boot time.
     *
     * @return void The method will not return any value.
     */
    public function boot()
    {
        $request = $this->app->get(Request::class);

        // Define constants.
        define("HORIZOM_VERSION", $this->app->version());
        define("HORIZOM_BASE_PATH", $request->basePath());
        define("HORIZOM_BASE_URL", $request->baseUrl());
    }
}
