<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Factory\Transport\Doctrine;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TMV\Messenger\Factory\Transport\Doctrine\DoctrineDBALTransportFactoryFactory;
use TMV\Messenger\Transport\Doctrine\DoctrineDBALTransportFactory;
use TypeError;

class DoctrineDBALTransportFactoryFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $factory = new DoctrineDBALTransportFactoryFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(DoctrineDBALTransportFactory::class, $service);
    }

    public function testFactoryWithoutContainer(): void
    {
        $this->expectException(TypeError::class);

        $factory = new DoctrineDBALTransportFactoryFactory();
        $factory(null);
    }
}
