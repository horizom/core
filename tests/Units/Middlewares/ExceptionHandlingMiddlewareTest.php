<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units\Middlewares;

use Horizom\Core\Contracts\ExceptionHandler;
use Horizom\Core\Middlewares\ExceptionHandlingMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

final class ExceptionHandlingMiddlewareTest extends TestCase
{
    private ExceptionHandler&MockObject $handler;
    private ExceptionHandlingMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->handler = $this->createMock(ExceptionHandler::class);
        $this->middleware = new ExceptionHandlingMiddleware($this->handler);
    }

    public function testPassesThroughWhenNoExceptionThrown(): void
    {
        $expectedResponse = new Response(200);
        $requestHandler = $this->makeHandler($expectedResponse);
        $request = new ServerRequest('GET', '/');

        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame($expectedResponse, $result);
    }

    public function testDelegatesExceptionToExceptionHandler(): void
    {
        $exception = new RuntimeException('Something went wrong');
        $errorResponse = new Response(500);

        $requestHandler = new class($exception) implements RequestHandlerInterface {
            public function __construct(private readonly \Throwable $exception) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->exception;
            }
        };

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($exception)
            ->willReturn($errorResponse);

        $request = new ServerRequest('GET', '/');
        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame($errorResponse, $result);
    }

    public function testPassesOriginalRequestToExceptionHandler(): void
    {
        $exception = new RuntimeException('Error');
        $capturedRequest = null;

        $requestHandler = new class($exception) implements RequestHandlerInterface {
            public function __construct(private readonly \Throwable $exception) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->exception;
            }
        };

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->willReturnCallback(
                function (\Throwable $e, ServerRequestInterface $req, RequestHandlerInterface $h) use (&$capturedRequest): ResponseInterface {
                    $capturedRequest = $req;
                    return new Response(500);
                }
            );

        $request = (new ServerRequest('POST', '/test'))->withHeader('X-Test', 'yes');
        $this->middleware->process($request, $requestHandler);

        $this->assertNotNull($capturedRequest);
        $this->assertSame('yes', $capturedRequest->getHeaderLine('X-Test'));
    }

    public function testHandlesErrorClassAsWellAsException(): void
    {
        $error = new \Error('Fatal error');

        $requestHandler = new class($error) implements RequestHandlerInterface {
            public function __construct(private readonly \Throwable $error) {}

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                throw $this->error;
            }
        };

        $this->handler
            ->expects($this->once())
            ->method('handle')
            ->with($error)
            ->willReturn(new Response(500));

        $request = new ServerRequest('GET', '/');
        $result = $this->middleware->process($request, $requestHandler);

        $this->assertSame(500, $result->getStatusCode());
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
