<?php

namespace Horizom\Core\Providers;

use Horizom\Core\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\Horizom\Core\View::class, function () {
            $paths = $this->getPaths();
            return new \Horizom\Core\View($paths->viewPaths, $paths->cachePath);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $paths = $this->getPaths();

        if (!is_dir($paths->cachePath)) {
            mkdir($paths->cachePath, 0755, true);
        }
    }

    public function getPaths()
    {
        return (object) [
            'viewPaths' => [base_path('resources/views')],
            'cachePath' => base_path('storage/cache/views'),
        ];
    }
}
