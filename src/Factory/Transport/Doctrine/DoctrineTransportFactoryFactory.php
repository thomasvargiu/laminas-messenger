<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport\Doctrine;

use Doctrine\Persistence\ConnectionRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use TMV\Laminas\Messenger\Factory\Transport\UnsupportedTransportFactory;

/**
 * @psalm-api
 */
final class DoctrineTransportFactoryFactory
{
    public function __invoke(ContainerInterface $container): TransportFactoryInterface
    {
        if (! class_exists(DoctrineTransportFactory::class)) {
            return new UnsupportedTransportFactory();
        }

        /** @var array{messenger?: array{doctrine_connection_registry: string}} $config */
        $config = $container->get('config');
        /** @var ConnectionRegistry $connectionRegistry */
        $connectionRegistry = $container->get($config['messenger']['doctrine_connection_registry']);

        return new DoctrineTransportFactory($connectionRegistry);
    }
}
