<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Retry;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Retry\RetryStrategyInterface;

final class RetryStrategyLocatorFactory
{
    public function __invoke(ContainerInterface $container): ContainerInterface
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $transports = $config['messenger']['transports'] ?? [];
        $factories = [];

        foreach ($transports as $name => $transport) {
            $factories[$name] = static function () use ($name, $container): RetryStrategyInterface {
                return (new RetryStrategyFactory($name))($container);
            };
        }

        return new ServiceManager(['factories' => $factories]);
    }
}
