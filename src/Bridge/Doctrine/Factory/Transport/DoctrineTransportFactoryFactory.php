<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Transport;

use Doctrine\Persistence\ConnectionRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Bridge\Doctrine\Transport\DoctrineTransportFactory;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use TMV\Laminas\Messenger\ConfigProvider;
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

        /** @var array{connection_registry?: string|null, manager_registry: string} $config */
        $config = $container->get('config')[ConfigProvider::CONFIG_KEY]['doctrine'] ?? [];
        /** @var ConnectionRegistry $connectionRegistry */
        $connectionRegistry = $container->get($config['connection_registry'] ?? $config['manager_registry']);

        return new DoctrineTransportFactory($connectionRegistry);
    }
}
