<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Middleware;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Middleware\SendMessageMiddleware;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Middleware\SendMessageMiddlewareFactory;

class SendMessageMiddlewareFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $factory = [SendMessageMiddlewareFactory::class, 'bus_name'];

        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'event_dispatcher' => 'messenger.event_dispatcher',
                'logger' => 'messenger.logger',
                'cache_pool_for_restart_signal' => 'messenger.cache_pool_for_restart_signal',
                'transports' => [],
            ],
        ]);

        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $container->get('messenger.event_dispatcher')
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());

        $container->get('messenger.logger')
            ->shouldBeCalled()
            ->willReturn($logger->reveal());

        $service = $factory($container->reveal());

        $this->assertInstanceOf(SendMessageMiddleware::class, $service);
    }

    public function testFactoryWithoutOptionalServices(): void
    {
        $factory = [SendMessageMiddlewareFactory::class, 'bus_name'];

        $container = $this->prophesize(ContainerInterface::class);

        $service = $factory($container->reveal());

        $this->assertInstanceOf(SendMessageMiddleware::class, $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [SendMessageMiddlewareFactory::class, 'bus_name'];
        $factory('foo');
    }
}
