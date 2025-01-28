<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Factory\Transport;

use Doctrine\Persistence\ConnectionRegistry;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Transport\DoctrineTransportFactoryFactory;

class DoctrineDBALTransportFactoryFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testFactory(): void
    {
        $config = [
            'messenger' => [
                'doctrine' => [
                    'connection_registry' => 'connection_registry',
                ],
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

    public function testFactoryWithManagerRegistry(): void
    {
        $config = [
            'messenger' => [
                'doctrine' => [
                    'manager_registry' => 'manager_registry',
                ],
            ],
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $container->get('config')->willReturn($config);
        $container->get('manager_registry')->willReturn($managerRegistry->reveal());
        $factory = new DoctrineTransportFactoryFactory();
        $service = $factory($container->reveal());

        $this->assertInstanceOf(DoctrineTransportFactory::class, $service);
    }
}
