<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Transport\Receiver;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\Factory\Transport\Receiver\ReceiversLocatorFactory;

class ReceiversLocatorFactoryTest extends TestCase
{
    use ProphecyTrait;
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

        $this->assertInstanceOf(ServiceProviderInterface::class, $service);

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
