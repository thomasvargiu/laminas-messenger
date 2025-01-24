<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\EventListener\DispatchPcntlSignalListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageForRetryListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnSigtermSignalListener;
use TMV\Laminas\Messenger\Factory\EventDispatcherFactory;

class EventDispatcherFactoryTest extends TestCase
{
    use ProphecyTrait;

    private function getListeners(): array
    {
        return [
            'event1' => new class implements EventSubscriberInterface {
                public static function getSubscribedEvents(): array
                {
                    return ['event1' => ['bar', -100]];
                }
            },
            'event2' => new class implements EventSubscriberInterface {
                public static function getSubscribedEvents(): array
                {
                    return ['event2' => ['bar', -100]];
                }
            },
            'event3' => new class implements EventSubscriberInterface {
                public static function getSubscribedEvents(): array
                {
                    return ['event3' => ['bar', -100]];
                }
            },
            'event4' => new class implements EventSubscriberInterface {
                public static function getSubscribedEvents(): array
                {
                    return ['event4' => ['bar', -100]];
                }
            },
            'event5' => new class implements EventSubscriberInterface {
                public static function getSubscribedEvents(): array
                {
                    return ['event5' => ['bar', -100]];
                }
            },
        ];
    }

    public function testFactoryWithDefaults(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->shouldBeCalled()->willReturn([
            'messenger' => [
            ],
        ]);

        $container->get(StopWorkerOnSigtermSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event1']);
        $container->get(DispatchPcntlSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event2']);
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event3']);

        $container->get(SendFailedMessageToFailureTransportListener::class)
            ->shouldNotBeCalled();

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        $this->assertCount(3, $instance->getListeners());
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

        $container->get(StopWorkerOnSigtermSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event1']);
        $container->get(DispatchPcntlSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event2']);
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event3']);
        $container->get(SendFailedMessageToFailureTransportListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event4']);

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        $this->assertCount(4, $instance->getListeners());
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

        $container->get(StopWorkerOnSigtermSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event1']);
        $container->get(DispatchPcntlSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event2']);
        $container->get(SendFailedMessageForRetryListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event3']);
        $container->get(StopWorkerOnRestartSignalListener::class)
            ->shouldBeCalled()
            ->willReturn($this->getListeners()['event4']);

        $factory = new EventDispatcherFactory();

        $instance = $factory($container->reveal());

        $this->assertCount(4, $instance->getListeners());
    }
}
