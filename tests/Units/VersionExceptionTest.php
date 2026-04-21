<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units;

use Horizom\Core\Exceptions\VersionException;
use PHPUnit\Framework\TestCase;

final class VersionExceptionTest extends TestCase
{
    public function testDefaultMessageContainsCurrentPhpVersion(): void
    {
        $e = new VersionException();
        $this->assertStringContainsString(PHP_VERSION, $e->getMessage());
        $this->assertStringContainsString('PHP 8.0', $e->getMessage());
    }

    public function testCustomMessageIsUsed(): void
    {
        $e = new VersionException('Custom message');
        $this->assertSame('Custom message', $e->getMessage());
    }

    public function testDefaultCodeIsZero(): void
    {
        $e = new VersionException();
        $this->assertSame(0, $e->getCode());
    }

    public function testCustomCodeIsUsed(): void
    {
        $e = new VersionException('msg', 42);
        $this->assertSame(42, $e->getCode());
    }

    public function testPreviousThrowableIsLinked(): void
    {
        $previous = new \RuntimeException('original');
        $e = new VersionException('wrapper', 0, $previous);
        $this->assertSame($previous, $e->getPrevious());
    }

    public function testExtendsRuntimeException(): void
    {
        $e = new VersionException();
        $this->assertInstanceOf(\RuntimeException::class, $e);
    }
}
