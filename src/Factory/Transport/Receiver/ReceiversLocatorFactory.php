<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport\Receiver;

use function array_key_exists;
use function array_keys;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

final class ReceiversLocatorFactory
{
    public function __invoke(ContainerInterface $container): ContainerInterface
    {
        $factories = [];

        $config = $container->has('config') ? $container->get('config') : [];
        $transportNames = array_keys($config['messenger']['transports'] ?? []);
        $receivers = $config['messenger']['receivers'] ?? [];

        foreach ($transportNames as $name) {
            $factories[(string) $name] = static function () use ($name, $container): ReceiverInterface {
                return $container->get((string) $name);
            };
        }

        foreach ($receivers as $name => $serviceName) {
            if (array_key_exists((string) $name, $factories)) {
                throw new InvalidArgumentException(sprintf('A receiver named "%s" already exists as a transport name', $name));
            }

            $factories[(string) $name] = static function () use ($serviceName, $container): ReceiverInterface {
                return $container->get($serviceName);
            };
        }

        return new ServiceManager(['factories' => $factories]);
    }
}
