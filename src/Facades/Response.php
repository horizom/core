<?php

declare(strict_types=1);

namespace Horizom\Core\Facades;

use Horizom\Core\Facades\Facade;

/**
 * @method static \Horizom\Http\Response create()
 * @method static \Horizom\Http\Response fromInstance(\Psr\Http\Message\ResponseInterface $response)
 * @method static \Psr\Http\Message\ResponseInterface view(string $name, array<string, mixed> $data = [], string $contentType = 'text/html')
 * @method static \Psr\Http\Message\ResponseInterface redirect(string $url, int $status = 302, array<string, string> $headers = [])
 * @method static \Psr\Http\Message\ResponseInterface json(mixed $data = [], int $status = 200, array<string, string> $headers = [], int $options = 0)
 * @method static \Psr\Http\Message\ResponseInterface render(string $view, array<string, mixed> $data = [], array<string, mixed> $mergeData = [])
 * @method static \Psr\Http\Message\ResponseInterface file(string|resource|\Psr\Http\Message\StreamInterface $file, mixed $contentType = true)
 * @method static \Psr\Http\Message\ResponseInterface download(string|resource|\Psr\Http\Message\StreamInterface $file, string $name = null, mixed $contentType = true)
 * @method static \Psr\Http\Message\ResponseInterface redirectWithBaseUrl(string $url = null, int $status = null)
 * @method static int getStatusCode()
 * @method static string getReasonPhrase()
 * @method static \Psr\Http\Message\ResponseInterface withStatus(int $code, string $reasonPhrase = '')
 * @method static string getProtocolVersion()
 * @method static \Psr\Http\Message\MessageInterface withProtocolVersion(string $version)
 * @method static array<string, string[]> getHeaders()
 * @method static bool hasHeader(string $header)
 * @method static array<int, string> getHeader(string $header)
 * @method static string getHeaderLine(string $header)
 * @method static \Psr\Http\Message\MessageInterface withHeader(string $header, mixed $value)
 * @method static \Psr\Http\Message\MessageInterface withAddedHeader(string $header, mixed $value)
 * @method static \Psr\Http\Message\MessageInterface withoutHeader(string $header)
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
    public static function getFacadeAccessor(): string
    {
        return \Horizom\Http\Response::class;
    }
}
