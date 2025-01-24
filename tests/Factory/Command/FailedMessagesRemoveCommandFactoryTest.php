<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Command\FailedMessagesRemoveCommandFactory;

class FailedMessagesRemoveCommandFactoryTest extends TestCase
{
    use ProphecyTrait;
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

        $receiversLocator = $this->prophesize(ServiceProviderInterface::class);
        $failedTransport = $this->prophesize(TransportInterface::class);
        $container->get('messenger.receivers_locator')
            ->shouldBeCalled()
            ->willReturn($receiversLocator->reveal());

        $factory = new FailedMessagesRemoveCommandFactory();

        $service = $factory($container->reveal());

        $this->assertInstanceOf(FailedMessagesRemoveCommand::class, $service);
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

        $factory = new FailedMessagesRemoveCommandFactory();

        $factory($container->reveal());
    }
}
