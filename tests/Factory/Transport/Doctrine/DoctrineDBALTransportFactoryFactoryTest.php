<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Factory\Transport\Doctrine;

use Doctrine\Persistence\ConnectionRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use TMV\Laminas\Messenger\Factory\Transport\Doctrine\DoctrineTransportFactoryFactory;

class DoctrineDBALTransportFactoryFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactory(): void
    {
        $config = [
            'messenger' => [
                'doctrine_connection_registry' => 'connection_registry',
            ],
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $connectionRegistry = $this->prophesize(ConnectionRegistry::class);
        $container->get('config')->willReturn($config);
        $container->get('connection_registry')->willReturn($connectionRegistry->reveal());
        $factory = new DoctrineTransportFactoryFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(DoctrineTransportFactory::class, $service);
    }
}
