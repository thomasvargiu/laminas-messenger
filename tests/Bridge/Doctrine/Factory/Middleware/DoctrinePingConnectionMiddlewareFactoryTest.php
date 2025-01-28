<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Factory\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware\DoctrinePingConnectionMiddlewareFactory;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrinePingConnectionMiddleware;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

class DoctrinePingConnectionMiddlewareFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactory(): void
    {
        $config = [
            'messenger' => [
                'doctrine' => [
                    'manager_registry' => ManagerRegistry::class,
                ],
            ],
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn($config);
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
