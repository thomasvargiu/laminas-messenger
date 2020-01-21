<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Factory\Transport\Receiver;

use Laminas\ServiceManager\ServiceManager;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Messenger\Exception\InvalidArgumentException;
use TMV\Messenger\Factory\Transport\Receiver\ReceiversLocatorFactory;

class ReceiversLocatorFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'transports' => [
                    'foo' => [],
                ],
                'receivers' => [
                    'bar' => 'bar_receiver',
                ],
            ],
        ]);

        $fooTransport = $this->prophesize(TransportInterface::class);
        $barReceiver = $this->prophesize(ReceiverInterface::class);

        $container->get('foo')
            ->shouldBeCalled()
            ->willReturn($fooTransport->reveal());

        $container->get('bar_receiver')
            ->shouldBeCalled()
            ->willReturn($barReceiver->reveal());

        $factory = new ReceiversLocatorFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(ServiceManager::class, $service);

        $this->assertSame($fooTransport->reveal(), $service->get('foo'));
        $this->assertSame($barReceiver->reveal(), $service->get('bar'));
    }

    public function testFactoryWithSameTransportAndReceiverNameShouldThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A receiver named "foo" already exists as a transport name');

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'transports' => [
                    'foo' => [],
                ],
                'receivers' => [
                    'foo' => 'bar_receiver',
                ],
            ],
        ]);

        $factory = new ReceiversLocatorFactory();
        $service = $factory($container->reveal());
    }
}
