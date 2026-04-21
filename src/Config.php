<?php

declare(strict_types=1);

namespace Horizom\Core;

use Illuminate\Support\Collection;

class Config extends Collection
{
    /**
     * Default configuration values.
     *
     * @var array<string, mixed>
     */
    private array $defaults = [
        'app.name' => 'Horizom',
        'app.env' => 'development',
        'app.base_path' => '',
        'app.base_url' => 'http://localhost:8000',
        'app.asset_url' => null,
        'app.timezone' => 'UTC',
        'app.locale' => 'en_US',
        'app.exception_handler' => false,
        'app.display_exception' => false,
        'providers' => [],
        'aliases' => [],
    ];

    /**
     * Create a new Config instance, merging defaults with any provided items.
     *
     * @param mixed $items
     */
    public function __construct(mixed $items = [])
    {
        $resolved = $this->getArrayableItems($items);
        parent::__construct(array_merge($this->defaults, $resolved));
    }
}
