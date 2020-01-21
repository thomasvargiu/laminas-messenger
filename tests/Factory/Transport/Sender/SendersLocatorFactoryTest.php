<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Factory\Transport\Sender;

use function iterator_to_array;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Sender\SendersLocator;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Messenger\Exception\LogicException;
use TMV\Messenger\Factory\Transport\Sender\SendersLocatorFactory;
use TMV\Messenger\Test\Factory\MessageMock;

class SendersLocatorFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'buses' => [
                    'bus_name' => [
                        'routes' => [
                            MessageMock::class => ['transport_name'],
                        ],
                    ],
                ],
                'transports' => [
                    'transport_name' => [],
                ],
            ],
        ]);

        $transport = $this->prophesize(TransportInterface::class);

        $container->has('transport_name')->willReturn(true);
        $container->get('transport_name')
            ->shouldBeCalled()
            ->willReturn($transport->reveal());

        $factory = new SendersLocatorFactory('bus_name');

        /** @var SendersLocator $service */
        $service = $factory($container->reveal());

        $this->assertInstanceOf(SendersLocator::class, $service);
        $senders = iterator_to_array($service->getSenders(new Envelope(new MessageMock())));

        $this->assertSame(['transport_name' => $transport->reveal()], $senders);
    }

    public function testFactoryWithInvalidRouteShouldThrowException(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Invalid Messenger routing configuration: class or interface "foo" not found.');

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'buses' => [
                    'bus_name' => [
                        'routes' => [
                            'foo' => [],
                        ],
                    ],
                ],
            ],
        ]);

        $factory = new SendersLocatorFactory('bus_name');
        $factory($container->reveal());
    }
}
