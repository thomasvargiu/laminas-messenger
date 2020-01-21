<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Command;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\StopWorkersCommand;
use TMV\Messenger\Exception\InvalidArgumentException;

final class StopWorkersCommandFactory
{
    public function __invoke(ContainerInterface $container): StopWorkersCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var string|null $cachePoolForRestartSignal */
        $cachePoolForRestartSignal = $config['messenger']['cache_pool_for_restart_signal'] ?? null;

        if (null === $cachePoolForRestartSignal) {
            throw new InvalidArgumentException('Invalid cache_pool_for_restart_signal name');
        }

        /** @var CacheItemPoolInterface $cachePoolForRestartSignal */
        $cachePoolForRestartSignal = $container->get($cachePoolForRestartSignal);

        return new StopWorkersCommand($cachePoolForRestartSignal);
    }
}
