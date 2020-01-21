<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Command;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Command\FailedMessagesShowCommandFactory;

class FailedMessagesShowCommandFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'failure_transport' => 'failed',
                'transports' => [],
            ],
        ]);

        $receiversLocator = $this->prophesize(ContainerInterface::class);
        $failedTransport = $this->prophesize(TransportInterface::class);
        $container->get('messenger.receivers_locator')
            ->shouldBeCalled()
            ->willReturn($receiversLocator->reveal());

        $receiversLocator->get('failed')
            ->shouldBeCalled()
            ->willReturn($failedTransport->reveal());

        $factory = new FailedMessagesShowCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(FailedMessagesShowCommand::class, $service);
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

        $factory = new FailedMessagesShowCommandFactory();

        $factory($container->reveal());
    }
}
