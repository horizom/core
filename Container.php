<?php

namespace Horizom\Core;

use DI\Container as DIContainer;
use Psr\Container\ContainerInterface;

/**
 * Horizom's default DI container is php-di/php-di.
 *
 * Horizom\App expects a container that implements Psr\Container\ContainerInterface
 * with these service keys configured and ready for use:
 *
 *  `version`           version number of the application.
 *  `request`           an instance of \Psr\Http\Message\ServerRequestInterface
 *  `response`          an instance of \Psr\Http\Message\ResponseInterface
 *  `callableResolver`  an instance of \Horizom\Interfaces\CallableResolverInterface
 *  `foundHandler`      an instance of \Horizom\Interfaces\InvocationStrategyInterface
 *  `errorHandler`      a callable with the signature: function($request, $response, $exception)
 *  `notFoundHandler`   a callable with the signature: function($request, $response)
 *  `notAllowedHandler` a callable with the signature: function($request, $response, $allowedHttpMethods)
 */
class Container extends DIContainer implements ContainerInterface
{
}
