<?php

declare(strict_types=1);

namespace Horizom\Core\Providers;

use Horizom\Core\Contracts\ExceptionHandler;
use Horizom\Core\Middlewares\ExceptionHandlingMiddleware;
use Horizom\Core\ServiceProvider;
use Horizom\Http\Request;
use Spatie\Ignition\Ignition;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }

    /**
     * Binds and sets up implementations at boot time.
     */
    public function boot(): void
    {
        $request = $this->app->get(Request::class);
        $shouldDisplayException = (bool) env('APP_DISPLAY_EXCEPTION', true);
        $shouldHandleException = (bool) env('APP_EXCEPTION_HANDLER', true);

        if ($shouldHandleException) {
            $this->registerMiddleware();
        }

        if ($request->wantsJson() || $request->isJson()) {
            $this->registerJsonExceptionHandler($shouldDisplayException);
        } else {
            $this->registerExceptionHandler($shouldDisplayException);
        }
    }

    protected function registerMiddleware(): void
    {
        $exceptionHandler = $this->app->get(ExceptionHandler::class);
        $this->app->add(new ExceptionHandlingMiddleware($exceptionHandler));
    }

    /**
     * Register exception handler.
     */
    protected function registerExceptionHandler(bool $shouldDisplayException): void
    {
        Ignition::make()->shouldDisplayException($shouldDisplayException)->register();
    }

    public function registerJsonExceptionHandler(bool $shouldDisplayException): void
    {
        error_reporting(-1);

        /** @phpstan-ignore argument.type */
        set_error_handler([$this, 'renderJsonError']);
        set_exception_handler([$this, 'handleJsonException']);
    }

    /**
     * @param int $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array<int, mixed> $context
     *
     * @return void
     * @throws \ErrorException
     */
    public function renderJsonError(
        int $level,
        string $message,
        string $file = '',
        int $line = 0,
        array $context = []
    ): void {
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Handle an exception and generate a JSON response.
     */
    public function handleJsonException(\Throwable $throwable): void
    {
        $report = $this->createJsonReport($throwable);
        $this->renderJsonReport($report);
    }

    /**
     * Create a JSON report.
     *
     * @return array<string, mixed>
     */
    public function createJsonReport(\Throwable $exception): array
    {
        return [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
        ];
    }

    /**
     * Render a JSON report.
     *
     * @param array<string, mixed> $report
     */
    public function renderJsonReport(array $report): void
    {
        $response = $this->app->make(\Horizom\Http\Response::class);
        $encoded = json_encode($report);
        $response->getBody()->write($encoded !== false ? $encoded : '{}');

        $this->app->emit(
            $response->withStatus(500)->withHeader('Content-Type', 'application/json')
        );
    }
}
