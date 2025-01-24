<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Command;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\StopWorkersCommand;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

/**
 * @psalm-api
 */
final class StopWorkersCommandFactory
{
    public function __invoke(ContainerInterface $container): StopWorkersCommand
    {
        /** @var array{messenger: array{cache_pool_for_restart_signal?: string}} $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $cachePoolForRestartSignalName = $config['messenger']['cache_pool_for_restart_signal'] ?? null;

        if (null === $cachePoolForRestartSignalName) {
            throw new InvalidArgumentException('Invalid cache_pool_for_restart_signal name');
        }

        /** @var CacheItemPoolInterface $cachePoolForRestartSignal */
        $cachePoolForRestartSignal = $container->get($cachePoolForRestartSignalName);

        return new StopWorkersCommand($cachePoolForRestartSignal);
    }
}
