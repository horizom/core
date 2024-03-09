<?php

namespace Horizom\Core\Middlewares;

use Horizom\Core\Contracts\ExceptionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ExceptionHandlingMiddleware implements MiddlewareInterface
{
    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    public function __construct(ExceptionHandler $exceptionHandler)
    {
        $this->exceptionHandler = $exceptionHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $request = app()->get(\Horizom\Http\Request::class);
            return $this->exceptionHandler->handle($e, $request, $handler);
        }
    }
}
