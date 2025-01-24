<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Transport\Doctrine;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransport;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use TMV\Laminas\Messenger\Transport\Doctrine\DoctrineDBALTransportFactory;

class DoctrineDBALTransportFactoryTest extends TestCase
{
    use ProphecyTrait;
    public function testCreateTransport(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);

        $dsn = 'doctrinedbal://hostname';
        $options = [];

        $connection = $this->prophesize(Connection::class);

        $container->get('hostname')->shouldBeCalled()->willReturn($connection->reveal());

        $factory = new DoctrineDBALTransportFactory($container->reveal());

        $transport = $factory->createTransport($dsn, $options, $serializer->reveal());

        $this->assertInstanceOf(DoctrineTransport::class, $transport);
    }

    public function testCreateTransportShouldFailOnConnectionNotFound(): void
    {
        $this->expectException(TransportException::class);
        $this->expectExceptionMessage('Could not find Doctrine connection from Messenger DSN "doctrinedbal://hostname"');

        $container = $this->prophesize(ContainerInterface::class);
        $serializer = $this->prophesize(SerializerInterface::class);

        $dsn = 'doctrinedbal://hostname';
        $options = [];

        $container->get('hostname')->shouldBeCalled()->willThrow(new InvalidArgumentException('No service'));

        $factory = new DoctrineDBALTransportFactory($container->reveal());

        $factory->createTransport($dsn, $options, $serializer->reveal());
    }

    public function testSupports(): void
    {
        $container = $this->prophesize(ContainerInterface::class);

        $factory = new DoctrineDBALTransportFactory($container->reveal());

        $this->assertTrue($factory->supports('doctrinedbal://hostname', []));
        $this->assertFalse($factory->supports('doctrinedbal2://hostname', []));
        $this->assertFalse($factory->supports('doctrine://hostname', []));
    }
}
