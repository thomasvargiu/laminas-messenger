<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Transport\Doctrine;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use TMV\Laminas\Messenger\Factory\Transport\Doctrine\DoctrineDBALTransportFactoryFactory;
use TMV\Laminas\Messenger\Transport\Doctrine\DoctrineDBALTransportFactory;
use TypeError;

class DoctrineDBALTransportFactoryFactoryTest extends TestCase
{
    use ProphecyTrait;
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
