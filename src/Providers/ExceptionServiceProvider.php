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
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Binds and sets up implementations at boot time.
     *
     * @return void The method will not return any value.
     */
    public function boot()
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

    protected function registerMiddleware()
    {
        $exceptionHandler = $this->app->get(ExceptionHandler::class);
        $this->app->add(new ExceptionHandlingMiddleware($exceptionHandler));
    }

    /**
     * Register exception handler.
     *
     * @param bool $shouldDisplayException
     *
     * @return void
     */
    protected function registerExceptionHandler(bool $shouldDisplayException)
    {
        Ignition::make()->shouldDisplayException($shouldDisplayException)->register();
    }

    public function registerJsonExceptionHandler(bool $shouldDisplayException)
    {
        error_reporting(-1);

        /** @phpstan-ignore-next-line  */
        set_error_handler([$this, 'renderJsonError']);

        /** @phpstan-ignore-next-line  */
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
     *
     * @param \Throwable $throwable
     * @return void
     */
    public function handleJsonException(\Throwable $throwable)
    {
        $report = $this->createJsonReport($throwable);
        $this->renderJsonReport($report);
    }

    /**
     * Create a JSON report.
     *
     * @param \Throwable $exception
     * @return array
     */
    public function createJsonReport(\Throwable $exception)
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
     * @param array $report
     * @return void
     */
    public function renderJsonReport(array $report)
    {
        $response = $this->app->make(\Horizom\Http\Response::class);
        $response->getBody()->write(json_encode($report));

        $this->app->emit(
            $response->withStatus(500)->withHeader('Content-Type', 'application/json')
        );
    }
}
