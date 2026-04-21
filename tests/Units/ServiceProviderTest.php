<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units;

use Horizom\Core\App;
use Horizom\Core\ServiceProvider;
use PHPUnit\Framework\TestCase;

final class ServiceProviderTest extends TestCase
{
    private App $app;

    protected function setUp(): void
    {
        parent::setUp();
        // Reset singleton between tests
        $reflection = new \ReflectionClass(App::class);
        $prop = $reflection->getProperty('instance');
        $prop->setAccessible(true);
        $prop->setValue(null, null);
    }

    public function testProvidesReturnsEmptyArrayByDefault(): void
    {
        $provider = $this->makeProvider();
        $this->assertSame([], $provider->provides());
    }

    public function testWhenReturnsEmptyArrayByDefault(): void
    {
        $provider = $this->makeProvider();
        $this->assertSame([], $provider->when());
    }

    public function testRegisterIsNoOpByDefault(): void
    {
        $provider = $this->makeProvider();
        // Should not throw
        $provider->register();
        $this->assertTrue(true);
    }

    public function testBootIsNoOpByDefault(): void
    {
        $provider = $this->makeProvider();
        // Should not throw
        $provider->boot();
        $this->assertTrue(true);
    }

    public function testDefaultProvidersReturnsCollection(): void
    {
        $providers = ServiceProvider::defaultProviders();
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $providers);
        $this->assertCount(3, $providers);
    }

    public function testDefaultProvidersContainsCoreProviders(): void
    {
        $providers = ServiceProvider::defaultProviders()->all();
        $this->assertContains(\Horizom\Core\Providers\CoreServiceProvider::class, $providers);
        $this->assertContains(\Horizom\Core\Providers\ExceptionServiceProvider::class, $providers);
        $this->assertContains(\Horizom\Core\Providers\ViewServiceProvider::class, $providers);
    }

    private function makeProvider(): ServiceProvider
    {
        $app = $this->createMock(App::class);

        return new class ($app) extends ServiceProvider {
        // concrete implementation of the abstract
        };
    }
}
