<?php

declare(strict_types=1);

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
            ['viewPaths' => $viewPaths, 'cachePath' => $cachePath] = $this->getPaths();
            return new \Horizom\Core\View($viewPaths, $cachePath);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ['cachePath' => $cachePath] = $this->getPaths();

        if (!is_dir($cachePath)) {
            mkdir($cachePath, 0755, true);
        }
    }

    /**
     * @return array{viewPaths: list<string>, cachePath: string}
     */
    public function getPaths(): array
    {
        return [
            'viewPaths' => [base_path('resources/views')],
            'cachePath' => base_path('storage/cache/views'),
        ];
    }
}
