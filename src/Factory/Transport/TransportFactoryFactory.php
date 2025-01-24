<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportFactory;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;

use function array_map;

/**
 * @psalm-api
 */
final class TransportFactoryFactory
{
    public function __invoke(ContainerInterface $container): TransportFactory
    {
        /** @var string[] $transportFactories */
        $transportFactories = $container->get('config')['messenger']['transport_factories'] ?? [];
        // $transportFactories = \array_filter($transportFactories, \Closure::fromCallable([$this, 'filterSyncTransport']));
        /** @var TransportFactoryInterface[] $factories */
        $factories = array_map([$container, 'get'], $transportFactories);

        return new TransportFactory($factories);
    }
}
