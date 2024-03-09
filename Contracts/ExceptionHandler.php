<?php

namespace Horizom\Core\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface ExceptionHandler
{
    public function handle(\Throwable $e, $request, RequestHandlerInterface $handler): ResponseInterface;
}
