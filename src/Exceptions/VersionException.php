<?php

declare(strict_types=1);

namespace Horizom\Core\Exceptions;

class VersionException extends \RuntimeException
{
    public function __construct(?string $message = null, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $message ?? 'This version of Horizom requires at least PHP 8.0 but you are currently running PHP ' . explode('-', PHP_VERSION)[0] . '. Please update your PHP version.',
            $code,
            $previous
        );
    }
}
