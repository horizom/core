<?php

namespace Horizom\Core\Exceptions;

class VersionException extends \Exception
{
    public function __construct(string $message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(
            "This version of Horizom requires at least PHP 8.0 but you are currently running PHP " . explode('-', PHP_VERSION)[0] . ". Please update your PHP version.", 
            $code, 
            $previous
        );
    }
}
