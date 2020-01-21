<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test;

use PHPUnit\Framework\TestCase;
use TMV\Laminas\Messenger\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    public function testGetDependencies(): void
    {
        $provider = new ConfigProvider();

        $dependencies = $provider->getDependencies();

        $this->assertArrayHasKey('aliases', $dependencies);
        $this->assertArrayHasKey('factories', $dependencies);
    }

    public function testInvoke(): void
    {
        $provider = new ConfigProvider();

        $dependencies = $provider->getDependencies();
        $config = $provider();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('dependencies', $config);
        $this->assertSame($dependencies, $config['dependencies']);
        $this->assertArrayHasKey('messenger', $config);
        $this->assertArrayHasKey('failure_transport', $config['messenger']);
        $this->assertArrayHasKey('event_dispatcher', $config['messenger']);
        $this->assertArrayHasKey('logger', $config['messenger']);
        $this->assertArrayHasKey('default_serializer', $config['messenger']);
        $this->assertArrayHasKey('cache_pool_for_restart_signal', $config['messenger']);
        $this->assertArrayHasKey('transport_factories', $config['messenger']);
        $this->assertArrayHasKey('buses', $config['messenger']);
        $this->assertArrayHasKey('transports', $config['messenger']);
    }
}
