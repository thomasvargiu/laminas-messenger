<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\RoutableMessageBus;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\RoutableMessageBusFactory;

class RoutableMessageBusFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $messageBus = $this->prophesize(MessageBusInterface::class);

        $container->has('bus_name')->willReturn(true);
        $container->get('bus_name')->shouldBeCalled()->willReturn($messageBus->reveal());

        $factory = [RoutableMessageBusFactory::class, 'bus_name'];
        $service = $factory($container->reveal());

        $this->assertInstanceOf(RoutableMessageBus::class, $service);
    }

    public function testFactoryWithoutFallback(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('bus_name')->willReturn(false);
        $container->get('bus_name')->shouldNotBeCalled();

        $factory = [RoutableMessageBusFactory::class, 'bus_name'];
        $service = $factory($container->reveal());

        $this->assertInstanceOf(RoutableMessageBus::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [RoutableMessageBusFactory::class, 'foo'];
        $factory('foo');
    }
}
