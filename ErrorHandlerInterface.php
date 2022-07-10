<?php

namespace Horizom\Core;

use Throwable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ErrorHandlerInterface
{
    public function handle(Throwable $e, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
