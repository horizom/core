<?php

namespace Horizom\Core;

use Illuminate\Support\Collection;

class Config extends Collection
{
    /**
     * The items contained in the collection.
     *
     * @var array
     */
    protected $items = [
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
}
