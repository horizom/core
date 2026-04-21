<?php

declare(strict_types=1);

namespace Horizom\Core\Tests\Units;

use Horizom\Core\Config;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = new Config();
    }

    public function testDefaultItemsArePresent(): void
    {
        $this->assertSame('Horizom', $this->config->get('app.name'));
        $this->assertSame('development', $this->config->get('app.env'));
        $this->assertSame('UTC', $this->config->get('app.timezone'));
        $this->assertSame('en_US', $this->config->get('app.locale'));
        $this->assertSame('http://localhost:8000', $this->config->get('app.base_url'));
    }

    public function testGetReturnsDefaultWhenKeyMissing(): void
    {
        $this->assertNull($this->config->get('non.existent'));
        $this->assertSame('fallback', $this->config->get('non.existent', 'fallback'));
    }

    public function testPutAddsNewKey(): void
    {
        $this->config->put('custom.key', 'custom_value');
        $this->assertSame('custom_value', $this->config->get('custom.key'));
    }

    public function testHasReturnsTrueForExistingKey(): void
    {
        $this->assertTrue($this->config->has('app.name'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->assertFalse($this->config->has('missing.key'));
    }

    public function testAllReturnsAllItems(): void
    {
        $all = $this->config->all();
        $this->assertIsArray($all);
        $this->assertArrayHasKey('app.name', $all);
        $this->assertArrayHasKey('providers', $all);
        $this->assertArrayHasKey('aliases', $all);
    }

    public function testMakeCreatesConfigFromArray(): void
    {
        $config = Config::make(['app.name' => 'MyApp', 'app.env' => 'production']);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame('MyApp', $config->get('app.name'));
        $this->assertSame('production', $config->get('app.env'));
    }

    public function testDefaultProvidersIsEmptyArray(): void
    {
        $this->assertSame([], $this->config->get('providers'));
    }

    public function testDefaultAliasesIsEmptyArray(): void
    {
        $this->assertSame([], $this->config->get('aliases'));
    }

    public function testNullableDefaultValues(): void
    {
        $this->assertNull($this->config->get('app.asset_url'));
        $this->assertFalse($this->config->get('app.exception_handler'));
        $this->assertFalse($this->config->get('app.display_exception'));
    }
}
