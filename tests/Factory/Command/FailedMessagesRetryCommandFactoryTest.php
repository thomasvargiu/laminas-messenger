<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Command\FailedMessagesRetryCommand;
use Symfony\Component\Messenger\RoutableMessageBus;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Command\FailedMessagesRetryCommandFactory;

class FailedMessagesRetryCommandFactoryTest extends TestCase
{
    use ProphecyTrait;
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'failure_transport' => 'failed',
                'event_dispatcher' => 'my.event_dispatcher',
                'logger' => 'my.logger',
                'transports' => [],
            ],
        ]);

        $receiversLocator = $this->prophesize(ServiceProviderInterface::class);
        $failedTransport = $this->prophesize(TransportInterface::class);
        $routableMessageBus = $this->prophesize(RoutableMessageBus::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $container->get('messenger.receivers_locator')
            ->shouldBeCalled()
            ->willReturn($receiversLocator->reveal());

        $container->get('messenger.routable_message_bus')
            ->shouldBeCalled()
            ->willReturn($routableMessageBus->reveal());

        $container->get('my.logger')
            ->shouldBeCalled()
            ->willReturn($logger->reveal());

        $container->get('my.event_dispatcher')
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());

        $factory = new FailedMessagesRetryCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(FailedMessagesRetryCommand::class, $service);
    }

    public function testFactoryWithoutOptionalDependencies(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'failure_transport' => 'failed',
                'event_dispatcher' => 'messenger.event_dispatcher',
                'logger' => null,
                'transports' => [],
            ],
        ]);

        $receiversLocator = $this->prophesize(ServiceProviderInterface::class);
        $failedTransport = $this->prophesize(TransportInterface::class);
        $routableMessageBus = $this->prophesize(RoutableMessageBus::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $container->get('messenger.receivers_locator')
            ->shouldBeCalled()
            ->willReturn($receiversLocator->reveal());

        $container->get('messenger.routable_message_bus')
            ->shouldBeCalled()
            ->willReturn($routableMessageBus->reveal());

        $container->get('messenger.logger')
            ->shouldNotBeCalled();

        $container->get('messenger.event_dispatcher')
            ->shouldBeCalled()
            ->willReturn($eventDispatcher->reveal());

        $factory = new FailedMessagesRetryCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(FailedMessagesRetryCommand::class, $service);
    }

    public function testFactoryWithNoFailureTransportShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid failure_transport name');

        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'failure_transport' => null,
            ],
        ]);

        $factory = new FailedMessagesRetryCommandFactory();

        $factory($container->reveal());
    }
}
