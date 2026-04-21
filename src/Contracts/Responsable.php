<?php

declare(strict_types=1);

namespace Horizom\Core\Contracts;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface Responsable
{
    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse(ServerRequestInterface $request): ResponseInterface;
}
