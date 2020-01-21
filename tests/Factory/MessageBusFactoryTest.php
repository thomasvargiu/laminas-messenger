<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\MessageBusFactory;

class MessageBusFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'event_dispatcher' => 'messenger.event_dispatcher',
                'buses' => [
                    'bus_name' => [
                        'middleware' => [
                            'middleware_name',
                        ],
                    ],
                ],
            ],
        ]);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $middleware = $this->prophesize(MiddlewareInterface::class);

        $container->get('messenger.event_dispatcher')
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());

        $container->get('middleware_name')
            ->shouldBeCalled()
            ->willReturn($middleware->reveal());

        $factory = [MessageBusFactory::class, 'bus_name'];
        /** @var MessageBus $service */
        $service = $factory($container->reveal());

        $this->assertInstanceOf(MessageBus::class, $service);
    }

    public function testFactoryWithNoDefaultMiddlewares(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'event_dispatcher' => 'messenger.event_dispatcher',
                'buses' => [
                    'bus_name' => [
                        'default_middleware' => false,
                        'middleware' => [
                            'middleware_name',
                        ],
                    ],
                ],
            ],
        ]);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $middleware = $this->prophesize(MiddlewareInterface::class);

        $container->get('messenger.event_dispatcher')
            ->shouldNotBeCalled()
            ->willReturn($eventDispatcher->reveal());

        $container->get('middleware_name')
            ->shouldBeCalled()
            ->willReturn($middleware->reveal());

        $factory = [MessageBusFactory::class, 'bus_name'];
        /** @var MessageBus $service */
        $service = $factory($container->reveal());

        $this->assertInstanceOf(MessageBus::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [MessageBusFactory::class, 'foo'];
        $factory('foo');
    }
}
