# horizom/core

<p align="center"><img src="https://horizom.github.io/img/horizom-logo-color.svg" width="400"></p>

<p align="center">
<a href="https://packagist.org/packages/horizom/core"><img src="https://poser.pugx.org/horizom/core/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/horizom/core"><img src="https://poser.pugx.org/horizom/core/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/horizom/core"><img src="https://poser.pugx.org/horizom/core/license.svg" alt="License"></a>
<img src="https://img.shields.io/badge/PHP-%5E8.0-blue" alt="PHP Version">
<img src="https://img.shields.io/badge/tested%20with-PHPUnit%2011-brightgreen" alt="PHPUnit">
</p>

The core of the **Horizom** framework - a PSR-oriented PHP 8 micro-framework built around a DI container, a service provider system, a router, and a PSR-15 middleware pipeline.

> **Note:** This repository contains the Horizom core code. To create a complete application, see the main repository [horizom/app](https://github.com/horizom/app).

---

## Table of Contents

- [horizom/core](#horizomcore)
  - [Table of Contents](#table-of-contents)
  - [Requirements](#requirements)
  - [Installation](#installation)
  - [Quick Start](#quick-start)
  - [Architecture](#architecture)
  - [Components](#components)
    - [Application (`App`)](#application-app)
      - [Service Binding](#service-binding)
      - [PSR-15 Middlewares](#psr-15-middlewares)
      - [Configuration](#configuration)
      - [Running](#running)
    - [Configuration (`Config`)](#configuration-config)
    - [DI Container (`Container`)](#di-container-container)
    - [Service Providers](#service-providers)
    - [Facades](#facades)
      - [Creating a Custom Facade](#creating-a-custom-facade)
    - [Middlewares](#middlewares)
      - [`BodyParsingMiddleware`](#bodyparsingmiddleware)
      - [`ContentLengthMiddleware`](#contentlengthmiddleware)
      - [`ExceptionHandlingMiddleware`](#exceptionhandlingmiddleware)
    - [Blade View (`View`)](#blade-view-view)
    - [Helpers](#helpers)
  - [Tests](#tests)
  - [License](#license)

---

## Requirements

| Dependency   | Minimum version |
| ------------ | --------------- |
| PHP          | 8.0             |
| ext-mbstring | \*              |
| ext-openssl  | \*              |

---

## Installation

```bash
composer require horizom/core
```

---

## Quick Start

```php
<?php

require 'vendor/autoload.php';

define('HORIZOM_ROOT', __DIR__);

use Horizom\Core\App;
use Horizom\Http\Request;

$app = new App(basePath: __DIR__);

// Register service providers
$app->register(\Horizom\Core\Providers\CoreServiceProvider::class);

// Add global middlewares
$app->add(\Horizom\Core\Middlewares\BodyParsingMiddleware::class);
$app->add(\Horizom\Core\Middlewares\ContentLengthMiddleware::class);

// Define routes through the router
$app->getRouter()->get('/', function () {
    return view('home', ['title' => 'Horizom']);
});

$app->run(Request::createFromGlobals());
```

---

## Architecture

```
horizom/core
├── src/
│   ├── App.php                         # Application entry point
│   ├── Config.php                      # Configuration storage (Collection)
│   ├── Container.php                   # Extended DI container (php-di)
│   ├── ServiceProvider.php             # Base provider class
│   ├── View.php                        # Blade adapter (illuminate/view)
│   ├── helpers.php                     # Utility global functions
│   ├── Contracts/                      # Framework interfaces
│   │   ├── ExceptionHandler.php
│   │   ├── FacadeContract.php
│   │   └── Responsable.php
│   ├── Exceptions/
│   │   └── VersionException.php
│   ├── Facades/                        # Static proxies to the container
│   │   ├── Config.php
│   │   ├── Facade.php
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── View.php
│   ├── Middlewares/                    # PSR-15 middlewares
│   │   ├── BodyParsingMiddleware.php
│   │   ├── ContentLengthMiddleware.php
│   │   └── ExceptionHandlingMiddleware.php
│   └── Providers/                      # Built-in providers
│       ├── ConfigServiceProvider.php
│       ├── CoreServiceProvider.php
│       ├── ExceptionServiceProvider.php
│       └── ViewServiceProvider.php
└── tests/
    └── Units/
```

---

## Components

### Application (`App`)

`App` is the framework's central singleton. It orchestrates the DI container, the middleware dispatcher, and the router.

```php
use Horizom\Core\App;
use Horizom\Http\Request;

$app = new App(basePath: __DIR__);

// Singleton - retrieve the existing instance
$app = App::getInstance();

// Framework version
echo $app->version(); // "4.0.0"
```

#### Service Binding

```php
// Simple binding
$app->bind(MyInterface::class, MyImplementation::class);

// Shared singleton
$app->singleton(CacheInterface::class, RedisCache::class);

// Already instantiated concrete instance
$app->instance(LoggerInterface::class, new FileLogger('/var/log/app.log'));

// Automatic resolution (autowiring)
$service = $app->make(MyService::class);

// Presence check
$app->has(MyInterface::class); // bool
```

#### PSR-15 Middlewares

```php
// Add a middleware to the global pipeline
$app->add(new \Horizom\Core\Middlewares\ContentLengthMiddleware());
$app->add(MyCustomMiddleware::class); // resolved through the container
```

#### Configuration

```php
// Read all configuration
$all = $app->config();

// Load a configuration file
// Loads config/database.php and merges the keys
$app->configure('database');

// Write one or more values
$app->config(['app.name' => 'My App', 'app.env' => 'production']);
```

#### Running

```php
// Runs the pipeline, dispatches the request, emits the HTTP response
$app->run(Request::createFromGlobals());
```

---

### Configuration (`Config`)

`Config` extends `Illuminate\Support\Collection` with default values merged automatically at construction time.

**Default values:**

| Key                     | Default value             |
| ----------------------- | ------------------------- |
| `app.name`              | `'Horizom'`               |
| `app.env`               | `'development'`           |
| `app.base_url`          | `'http://localhost:8000'` |
| `app.timezone`          | `'UTC'`                   |
| `app.locale`            | `'en_US'`                 |
| `app.exception_handler` | `false`                   |
| `app.display_exception` | `false`                   |
| `providers`             | `[]`                      |
| `aliases`               | `[]`                      |

```php
use Horizom\Core\Facades\Config;

// Read with a default value
$name = Config::get('app.name', 'MyApp');

// Write
Config::put('app.debug', true);

// Check whether a key exists
Config::has('app.timezone'); // true

// Read through the helper
$tz = config('app.timezone', 'UTC');
```

---

### DI Container (`Container`)

`Container` extends `DI\Container` (php-di) by adding service provider management.

```php
use Horizom\Core\Container;
use DI\ContainerBuilder;

$builder = new ContainerBuilder(Container::class);
$container = $builder->useAutowiring(true)->build();

// Register a provider
$provider = new MyServiceProvider($app);
$container->register($provider);

// Check whether a provider is loaded
$container->isLoaded(MyServiceProvider::class); // bool

// Retrieve a registered provider
$provider = $container->getProvider(MyServiceProvider::class);

// Boot all providers
$container->boot();
```

---

### Service Providers

Service providers encapsulate feature initialization. Two methods are available: `register()` for DI binding and `boot()` for post-registration initialization.

```php
<?php

declare(strict_types=1);

namespace App\Providers;

use Horizom\Core\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind interfaces to their implementations
        $this->app->singleton(
            \App\Contracts\DatabaseInterface::class,
            \App\Services\PdoDatabase::class
        );
    }

    public function boot(): void
    {
        // Code executed after all providers have been registered
        $db = $this->app->get(\App\Contracts\DatabaseInterface::class);
        $db->connect();
    }

    public function provides(): array
    {
        return [\App\Contracts\DatabaseInterface::class];
    }
}
```

Registration in configuration:

```php
// config/app.php  (or via App::config())
return [
    'providers' => [
        App\Providers\DatabaseServiceProvider::class,
    ],
];
```

Default providers:

| Provider                   | Role                                     |
| -------------------------- | ---------------------------------------- |
| `CoreServiceProvider`      | Defines the `HORIZOM_*` constants        |
| `ExceptionServiceProvider` | Configures exception handling (Ignition) |
| `ViewServiceProvider`      | Initializes the Blade template engine    |

---

### Facades

Facades provide convenient static access to container services without tight coupling.

```php
use Horizom\Core\Facades\Config;
use Horizom\Core\Facades\Request;
use Horizom\Core\Facades\Response;
use Horizom\Core\Facades\View;

// Config
$locale = Config::get('app.locale');

// Request
$email  = Request::input('email');
$token  = Request::bearerToken();

// Response
return Response::json(['status' => 'ok']);
return Response::redirect('/dashboard', 302);

// View
$html = View::render('emails.welcome', ['user' => $user]);
```

#### Creating a Custom Facade

```php
<?php

declare(strict_types=1);

namespace App\Facades;

use Horizom\Core\Facades\Facade;

class Cache extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \App\Services\CacheService::class;
    }
}
```

---

### Middlewares

All middlewares implement `Psr\Http\Server\MiddlewareInterface`.

#### `BodyParsingMiddleware`

Automatically parses the request body according to `Content-Type`.

| Content-Type                        | Result             |
| ----------------------------------- | ------------------ |
| `application/json`                  | `array`            |
| `application/x-www-form-urlencoded` | `array`            |
| `application/xml` / `text/xml`      | `SimpleXMLElement` |

```php
use Horizom\Core\Middlewares\BodyParsingMiddleware;

$middleware = new BodyParsingMiddleware();

// Register a custom parser
$middleware->registerBodyParser('application/msgpack', function (string $body): array {
    return msgpack_unpack($body);
});

$app->add($middleware);
```

#### `ContentLengthMiddleware`

Automatically adds the `Content-Length` header if it is missing.

```php
$app->add(new \Horizom\Core\Middlewares\ContentLengthMiddleware());
```

#### `ExceptionHandlingMiddleware`

Delegates unhandled exceptions to an `ExceptionHandler` injected through DI.

```php
use Horizom\Core\Contracts\ExceptionHandler;
use Horizom\Core\Middlewares\ExceptionHandlingMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyExceptionHandler implements ExceptionHandler
{
    public function handle(
        \Throwable $e,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Build a custom error response
    }
}

$app->instance(ExceptionHandler::class, new MyExceptionHandler());
$app->add(new ExceptionHandlingMiddleware($app->get(ExceptionHandler::class)));
```

---

### Blade View (`View`)

Adapter around `illuminate/view` to use the Blade template engine without Laravel.

```php
use Horizom\Core\View;

$view = new View(
    viewPaths: [__DIR__ . '/resources/views'],
    cachePath: __DIR__ . '/storage/cache/views',
);

// Render a view
echo $view->render('welcome', ['name' => 'Alice']);

// Custom directive
$view->directive('datetime', fn($expr) => "<?php echo ($expr)->format('d/m/Y H:i'); ?>");

// Share global data
$view->share('appName', config('app.name'));
```

Through the global helper:

```php
// In a route handler
return view('dashboard', ['user' => $user]);
```

Through the facade:

```php
use Horizom\Core\Facades\View;

$html = View::render('emails.confirmation', compact('order'));
```

---

### Helpers

Global functions available throughout the application.

| Function                | Description                            |
| ----------------------- | -------------------------------------- |
| `app()`                 | Returns the `App` instance             |
| `config($key, $def)`    | Reads a configuration value            |
| `url($path)`            | Generates an absolute URL              |
| `asset($path)`          | Generates the URL for a public asset   |
| `view($name, $data)`    | Renders a view and writes it to output |
| `bcrypt($value)`        | Hashes a value with Bcrypt             |
| `base_path($path)`      | Absolute path from the project root    |
| `storage_path($path)`   | Path to `storage/`                     |
| `public_path($path)`    | Path to `public/`                      |
| `resources_path($path)` | Path to `resources/`                   |

```php
$url     = url('users/profile');          // http://localhost:8000/users/profile
$asset   = asset('css/app.css');          // http://localhost:8000/css/app.css
$storage = storage_path('logs/app.log');  // /path/to/project/storage/logs/app.log
$hash    = bcrypt('my-secret-password');
```

---

## Tests

```bash
# Run the test suite
vendor/bin/phpunit

# With detailed output
vendor/bin/phpunit --testdox

# With code coverage (requires Xdebug)
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage
```

The suite covers:

| Class                         | Test cases                                                 |
| ----------------------------- | ---------------------------------------------------------- |
| `Config`                      | Default values, merging, read/write/exists                 |
| `Container`                   | Registration, force-register, boot, `isLoaded`             |
| `ServiceProvider`             | `provides`, `when`, `register`, `boot`, `defaultProviders` |
| `VersionException`            | Default message, code, `\Throwable` chaining               |
| `BodyParsingMiddleware`       | JSON, form-encoded, unknown type, custom parser            |
| `ContentLengthMiddleware`     | Header injection, no overwrite when already present        |
| `ExceptionHandlingMiddleware` | Pass-through, delegation, request context propagation      |

---

## License

Horizom Core is open-source software released under the [MIT license](LICENSE).
