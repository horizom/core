<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

use Horizom\Core\Facades\Facade;

/**
 * @method static \Horizom\Http\Response create()
 * @method static \Horizom\Http\Response fromInstance(\Psr\Http\Message\ResponseInterface $response)
 * @method static \Psr\Http\Message\ResponseInterface view(string $name, array $data = [], $contentType = 'text/html')
 * @method static \Psr\Http\Message\ResponseInterface redirect(string $url, int $status = 302, array $headers = [])
 * @method static \Psr\Http\Message\ResponseInterface json($data = [], int $status = 200, array $headers = [], int $options = 0)
 * @method static \Psr\Http\Message\ResponseInterface render(string $view, array $data = [], array $mergeData = [])
 * @method static \Psr\Http\Message\ResponseInterface file(string|resource|\Psr\Http\Message\StreamInterface $file, $contentType = true)
 * @method static \Psr\Http\Message\ResponseInterface download(string|resource|\Psr\Http\Message\StreamInterface $file, string $name = null, $contentType = true)
 * @method statix \Psr\Http\Message\ResponseInterface redirectWithBaseUrl($url = null, int $status = null)
 * @method static int getStatusCode()
 * @method static string getReasonPhrase()
 * @method static \Psr\Http\Message\ResponseInterface withStatus($code, $reasonPhrase = '')
 * @method static string getProtocolVersion()
 * @method static \Psr\Http\Message\MessageInterface withProtocolVersion($version)
 * @method static array getHeaders()
 * @method static bool hasHeader($header)
 * @method static array getHeader($header)
 * @method static string getHeaderLine($header)
 * @method static \Psr\Http\Message\MessageInterface withHeader($header, $value)
 * @method static \Psr\Http\Message\MessageInterface withAddedHeader($header, $value)
 * @method static \Psr\Http\Message\MessageInterface withoutHeader($header)
 * @method static \Psr\Http\Message\StreamInterface getBody()
 * @method static \Psr\Http\Message\MessageInterface withBody(\Psr\Http\Message\StreamInterface $body)
 *
 * @see \Horizom\Http\Response
 */
class Response extends Facade
{
    /**
     * Get the root object behind the facade.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Horizom\Http\Response::class;
    }
}
