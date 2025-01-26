<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportFactory;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;

/**
 * @psalm-api
 */
final class TransportFactoryFactory
{
    public function __invoke(ContainerInterface $container): TransportFactory
    {
        /** @var array<string> $transportFactories */
        $transportFactories = $container->get('config')['messenger']['transport_factories'] ?? [];
        /** @var TransportFactoryInterface[] $factories */
        $factories = [];

        foreach ($transportFactories as $name) {
            if (! $container->has($name)) {
                continue;
            }
            try {
                /** @var mixed $factory */
                $factory = $container->get($name);
                if ($factory instanceof TransportFactoryInterface) {
                    $factories[] = $factory;
                }
            } catch (ContainerExceptionInterface $e) {
                /** ignore */
            }
        }

        return new TransportFactory($factories);
    }
}
