<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\ServiceProvider;

class TransportProviderFactory
{
    public function __invoke(ContainerInterface $container): ServiceProviderInterface
    {
        $factories = [];

        /** @var array{messenger: array{transports?: array<string, mixed>}} $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $transportNames = array_keys($config['messenger']['transports'] ?? []);

        foreach ($transportNames as $name) {
            $factories[(string) $name] = static function () use ($name, $container): TransportInterface {
                /** @var TransportInterface $transport */
                $transport = $container->get((string) $name);

                return $transport;
            };
        }

        return new ServiceProvider(new ServiceManager(['factories' => $factories]), $factories);
    }
}
