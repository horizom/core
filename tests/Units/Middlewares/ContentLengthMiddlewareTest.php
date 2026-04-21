<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units\Middlewares;

use Horizom\Core\Middlewares\ContentLengthMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class ContentLengthMiddlewareTest extends TestCase
{
    private ContentLengthMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ContentLengthMiddleware();
    }

    public function testAddsContentLengthHeaderWhenBodyHasSize(): void
    {
        $body = Stream::create('Hello World');
        $response = (new Response(200))->withBody($body);
        $handler = $this->makeHandler($response);
        $request = new ServerRequest('GET', '/');

        $result = $this->middleware->process($request, $handler);

        $this->assertTrue($result->hasHeader('Content-Length'));
        $this->assertSame('11', $result->getHeaderLine('Content-Length'));
    }

    public function testDoesNotOverrideExistingContentLengthHeader(): void
    {
        $body = Stream::create('Hello World');
        $response = (new Response(200))
            ->withBody($body)
            ->withHeader('Content-Length', '999');
        $handler = $this->makeHandler($response);
        $request = new ServerRequest('GET', '/');

        $result = $this->middleware->process($request, $handler);

        $this->assertSame('999', $result->getHeaderLine('Content-Length'));
    }

    public function testEmptyBodyHasZeroContentLength(): void
    {
        $body = Stream::create('');
        $response = (new Response(200))->withBody($body);
        $handler = $this->makeHandler($response);
        $request = new ServerRequest('GET', '/');

        $result = $this->middleware->process($request, $handler);

        $this->assertTrue($result->hasHeader('Content-Length'));
        $this->assertSame('0', $result->getHeaderLine('Content-Length'));
    }

    public function testPassesThroughResponseUnchangedExceptForHeader(): void
    {
        $response = (new Response(404))->withHeader('X-Custom', 'test');
        $handler = $this->makeHandler($response);
        $request = new ServerRequest('GET', '/');

        $result = $this->middleware->process($request, $handler);

        $this->assertSame(404, $result->getStatusCode());
        $this->assertSame('test', $result->getHeaderLine('X-Custom'));
    }

    private function makeHandler(ResponseInterface $response): RequestHandlerInterface
    {
        return new class($response) implements RequestHandlerInterface {
            public function __construct(private readonly ResponseInterface $response) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };
    }
}
