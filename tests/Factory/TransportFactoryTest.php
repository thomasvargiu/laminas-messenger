<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Factory\Transport;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactory as SFTransportFactory;
use Symfony\Component\Messenger\Transport\TransportInterface;
use TMV\Messenger\Exception\InvalidArgumentException;
use TMV\Messenger\Factory\Transport\TransportFactory;

class TransportFactoryTest extends TestCase
{
    public function testStaticFactory(): void
    {
        $factory = [TransportFactory::class, 'foo'];
        $dsn = 'foo://bar';
        $options = ['foo2' => 'bar'];
        $serializerName = 'transport_serializer';
        $defaultSerializerName = 'serializer';

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'default_serializer' => $defaultSerializerName,
                'transports' => [
                    'foo' => [
                        'dsn' => $dsn,
                        'options' => $options,
                        'serializer' => $serializerName,
                    ],
                ],
            ],
        ]);

        $sfTransportFactory = $this->prophesize(SFTransportFactory::class);
        $transport = $this->prophesize(TransportInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);

        $container->get(SFTransportFactory::class)
            ->willReturn($sfTransportFactory->reveal());
        $container->get($serializerName)
            ->willReturn($serializer->reveal());

        $sfTransportFactory->createTransport($dsn, $options, $serializer->reveal())
            ->willReturn($transport->reveal());

        /** @var TransportInterface $service */
        $service = $factory($container->reveal());

        $this->assertSame($transport->reveal(), $service);
    }

    public function testStaticFactoryWithDsnStringAndDefaultSerializer(): void
    {
        $factory = [TransportFactory::class, 'foo'];
        $dsn = 'foo://bar';
        $options = [];
        $defaultSerializerName = 'serializer';

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'default_serializer' => $defaultSerializerName,
                'transports' => [
                    'foo' => $dsn,
                ],
            ],
        ]);

        $sfTransportFactory = $this->prophesize(SFTransportFactory::class);
        $transport = $this->prophesize(TransportInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);

        $container->get(SFTransportFactory::class)
            ->willReturn($sfTransportFactory->reveal());
        $container->get($defaultSerializerName)
            ->willReturn($serializer->reveal());

        $sfTransportFactory->createTransport($dsn, $options, $serializer->reveal())
            ->willReturn($transport->reveal());

        /** @var TransportInterface $service */
        $service = $factory($container->reveal());

        $this->assertSame($transport->reveal(), $service);
    }

    public function testStaticFactoryWithOnlyDsnString(): void
    {
        $dsn = 'foo://bar';
        $factory = [TransportFactory::class, $dsn];
        $options = [];
        $defaultSerializerName = 'serializer';

        $container = $this->prophesize(ContainerInterface::class);
        $container->has('config')->willReturn(true);
        $container->get('config')->willReturn([
            'messenger' => [
                'default_serializer' => $defaultSerializerName,
            ],
        ]);

        $sfTransportFactory = $this->prophesize(SFTransportFactory::class);
        $transport = $this->prophesize(TransportInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);

        $container->get(SFTransportFactory::class)
            ->willReturn($sfTransportFactory->reveal());
        $container->get($defaultSerializerName)
            ->willReturn($serializer->reveal());

        $sfTransportFactory->createTransport($dsn, $options, $serializer->reveal())
            ->willReturn($transport->reveal());

        /** @var TransportInterface $service */
        $service = $factory($container->reveal());

        $this->assertSame($transport->reveal(), $service);
    }

    public function testInvalidCall(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $factory = [TransportFactory::class, 'foo://'];
        $factory('foo');
    }
}
