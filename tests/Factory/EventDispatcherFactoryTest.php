<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Factory;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\EventListener\SendFailedMessageForRetryListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;
use TMV\Messenger\Factory\EventDispatcherFactory;

class EventDispatcherFactoryTest extends TestCase
{
    public function testFactoryWithDefaults(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->shouldBeCalled()->willReturn([
            'messenger' => [
            ],
        ]);

        $sendFailedMessageForRetryListener = new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return ['foo' => ['bar', -100]];
            }
        };
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($sendFailedMessageForRetryListener);

        $container->get(SendFailedMessageToFailureTransportListener::class)
            ->shouldNotBeCalled();

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        // this should instantiate the service
        $listeners = $instance->getListeners('foo');

        $this->assertCount(1, $listeners);
    }

    public function testFactoryWithFailureTransport(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->shouldBeCalled()->willReturn([
            'messenger' => [
                'failure_transport' => 'failure-transport-name',
            ],
        ]);

        $listener = new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return ['foo' => ['bar', -100]];
            }
        };

        $sendFailedMessageForRetryListener = new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return ['default1' => ['bar', -100]];
            }
        };
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($sendFailedMessageForRetryListener);

        $container->get(SendFailedMessageToFailureTransportListener::class)
            ->shouldBeCalled()
            ->willReturn($listener);

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        // this should instantiate the service
        $listeners = $instance->getListeners('foo');

        $this->assertCount(1, $listeners);
        $this->assertSame([$listener, 'bar'], $listeners[0]);
    }

    public function testFactoryWithStopWorkerListener(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->shouldBeCalled()->willReturn([
            'messenger' => [
                'cache_pool_for_restart_signal' => 'cache-service',
            ],
        ]);

        $listener = new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return ['foo' => ['bar', -100]];
            }
        };

        $sendFailedMessageForRetryListener = new class() implements EventSubscriberInterface {
            public static function getSubscribedEvents(): array
            {
                return ['default1' => ['bar', -100]];
            }
        };
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($sendFailedMessageForRetryListener);

        $container->get(StopWorkerOnRestartSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($listener);

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        // this should instantiate the service
        $listeners = $instance->getListeners('foo');

        $this->assertCount(1, $listeners);
        $this->assertSame([$listener, 'bar'], $listeners[0]);
    }
}
