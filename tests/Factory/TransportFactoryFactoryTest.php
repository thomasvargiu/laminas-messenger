<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Transport;

use PHPUnit\Framework\TestCase;
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
                ],
            ],
        ]);

        $fooFactory = $this->prophesize(TransportFactoryInterface::class);
        $fooFactory->supports('foo', [])->willReturn(true);
        $fooFactory->supports('bar', [])->willReturn(false);

        $container->get('foo_factory')
            ->shouldBeCalled()
            ->willReturn($fooFactory->reveal());

        $factory = new TransportFactoryFactory();

        $service = $factory($container->reveal());

        $this->assertTrue($service->supports('foo', []));
        $this->assertFalse($service->supports('bar', []));
    }
}
