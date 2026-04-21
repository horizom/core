<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units;

use Horizom\Core\App;
use Horizom\Core\Container;
use Horizom\Core\ServiceProvider;
use PHPUnit\Framework\TestCase;

final class ContainerTest extends TestCase
{
    private Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = new Container();
    }

    public function testRegisterAddsProviderAndReturnsIt(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $returned = $this->container->register($provider);

        $this->assertSame($provider, $returned);
    }

    public function testRegisterDoesNotRegisterSameProviderTwice(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $first = $this->container->register($provider);
        $second = $this->container->register($provider);

        $this->assertSame($first, $second);
        $this->assertCount(1, $this->container->getProviders($provider));
    }

    public function testRegisterWithForceAllowsDuplicate(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->container->register($provider);
        $this->container->register($provider, true);

        $this->assertCount(2, $this->container->getProviders($provider));
    }

    public function testGetProviderReturnsRegisteredProvider(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->container->register($provider);

        $found = $this->container->getProvider($provider);
        $this->assertSame($provider, $found);
    }

    public function testGetProviderReturnsNullWhenNotRegistered(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->assertNull($this->container->getProvider($provider));
    }

    public function testGetProvidersReturnsEmptyArrayWhenNoneRegistered(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->assertSame([], $this->container->getProviders($provider));
    }

    public function testIsLoadedReturnsTrueAfterRegistration(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->container->register($provider);

        $this->assertTrue($this->container->isLoaded($provider::class));
    }

    public function testIsLoadedReturnsFalseBeforeRegistration(): void
    {
        $app = $this->createMock(App::class);
        $provider = $this->makeProvider($app);

        $this->assertFalse($this->container->isLoaded($provider::class));
    }

    public function testBootCallsBootOnAllProviders(): void
    {
        $app = $this->createMock(App::class);

        $bootCalled = false;
        $provider = new class ($app) extends ServiceProvider {
            public bool $booted = false;

            public function boot(): void
            {
                $this->booted = true;
            }
        };

        $this->container->register($provider);
        $this->container->boot();

        $this->assertTrue($provider->booted);
    }

    public function testBootWithNoProvidersDoesNotFail(): void
    {
        // Should not throw
        $this->container->boot();
        $this->assertTrue(true);
    }

    private function makeProvider(App $app): ServiceProvider
    {
        return new class ($app) extends ServiceProvider {
        // concrete implementation
        };
    }
}
