<?php

declare(strict_types=1);

namespace Horizom\Core\Contracts;

interface FacadeContract
{
    /**
     * Get the registered name of the component.
     */
    public static function getFacadeAccessor(): string;

    /**
     * Get the root object behind the facade.
     */
    public static function getFacadeRoot(): mixed;
}
