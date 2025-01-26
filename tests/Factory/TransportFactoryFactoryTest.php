<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use TMV\Laminas\Messenger\Factory\Transport\TransportFactoryFactory;

class TransportFactoryFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get('config')->willReturn([
            'messenger' => [
                'transport_factories' => [
                    'foo_factory',
                    TransportFactoryInterface::class,
                ],
            ],
        ]);

        $transport = $this->prophesize(TransportFactoryInterface::class);
        $transport->supports('foo', Argument::cetera())->willReturn(true);

        $container->has('foo_factory')->willReturn(false);
        $container->get('foo_factory')
            ->shouldNotBeCalled();

        $container->has(TransportFactoryInterface::class)->willReturn(true);
        $container->get(TransportFactoryInterface::class)
            ->shouldBeCalled()
            ->willReturn($transport->reveal());

        $factory = new TransportFactoryFactory();

        $service = $factory($container->reveal());

        $this->assertTrue($service->supports('foo', []));
    }
}
