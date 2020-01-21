<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Middleware;

use Doctrine\Common\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Middleware\DoctrinePingConnectionMiddlewareFactory;
use TMV\Laminas\Messenger\Middleware\DoctrinePingConnectionMiddleware;

class DoctrinePingConnectionMiddlewareFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $container->get(ManagerRegistry::class)
            ->shouldBeCalled()
            ->willReturn($managerRegistry->reveal());

        $factory = [DoctrinePingConnectionMiddlewareFactory::class, 'connection_name'];
        $service = $factory($container->reveal());

        $this->assertInstanceOf(DoctrinePingConnectionMiddleware::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [DoctrinePingConnectionMiddlewareFactory::class, 'connection_name'];
        $factory('foo');
    }
}
