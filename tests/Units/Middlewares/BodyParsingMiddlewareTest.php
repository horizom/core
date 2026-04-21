<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units\Middlewares;

use Horizom\Core\Middlewares\BodyParsingMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class BodyParsingMiddlewareTest extends TestCase
{
    private BodyParsingMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new BodyParsingMiddleware();
    }

    public function testParsesJsonBody(): void
    {
        $json = json_encode(['name' => 'Alice', 'age' => 30]);
        $request = $this->makeRequest('application/json', $json);

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertSame(['name' => 'Alice', 'age' => 30], $parsed);
    }

    public function testParsesFormEncodedBody(): void
    {
        $request = $this->makeRequest('application/x-www-form-urlencoded', 'foo=bar&baz=qux');

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $parsed);
    }

    public function testDoesNotOverrideAlreadyParsedBody(): void
    {
        $request = (new ServerRequest('POST', '/'))
            ->withHeader('Content-Type', 'application/json')
            ->withBody(Stream::create('{"key":"value"}'))
            ->withParsedBody(['existing' => 'data']);

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        // Should keep existing parsed body
        $this->assertSame(['existing' => 'data'], $parsed);
    }

    public function testReturnsNullForUnknownContentType(): void
    {
        $request = $this->makeRequest('text/plain', 'some plain text');

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertNull($parsed);
    }

    public function testReturnsNullWhenNoContentTypeHeader(): void
    {
        $request = (new ServerRequest('POST', '/'))
            ->withBody(Stream::create('data'));

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertNull($parsed);
    }

    public function testReturnsNullForInvalidJson(): void
    {
        $request = $this->makeRequest('application/json', 'not-valid-json');

        $parsed = 'sentinel';
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertNull($parsed);
    }

    public function testCustomBodyParserIsRegistered(): void
    {
        $this->middleware->registerBodyParser('application/custom', static function (string $input): array {
            return ['custom' => $input];
        });

        $request = $this->makeRequest('application/custom', 'my-data');

        $parsed = null;
        $handler = $this->makeHandlerCapturing($parsed);

        $this->middleware->process($request, $handler);

        $this->assertSame(['custom' => 'my-data'], $parsed);
    }

    public function testHasBodyParserReturnsTrueForRegisteredType(): void
    {
        $this->assertTrue($this->middleware->hasBodyParser('application/json'));
    }

    public function testHasBodyParserReturnsFalseForUnregisteredType(): void
    {
        $this->assertFalse($this->middleware->hasBodyParser('application/unknown'));
    }

    public function testGetBodyParserThrowsForUnregisteredType(): void
    {
        $this->expectException(RuntimeException::class);
        $this->middleware->getBodyParser('application/unknown');
    }

    public function testGetBodyParserReturnsCallableForRegisteredType(): void
    {
        $parser = $this->middleware->getBodyParser('application/json');
        $this->assertIsCallable($parser);
    }

    private function makeRequest(string $contentType, string $body): ServerRequestInterface
    {
        return (new ServerRequest('POST', '/'))
            ->withHeader('Content-Type', $contentType)
            ->withBody(Stream::create($body));
    }

    private function makeHandlerCapturing(mixed &$capturedParsedBody): RequestHandlerInterface
    {
        return new class ($capturedParsedBody) implements RequestHandlerInterface {
            public function __construct(private mixed &$captured)
            {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->captured = $request->getParsedBody();
                return new Response(200);
            }
        };
    }
}
