<?php

declare(strict_types=1);

namespace Horizom\Core\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ExceptionHandler
{
    public function handle(\Throwable $e, ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
