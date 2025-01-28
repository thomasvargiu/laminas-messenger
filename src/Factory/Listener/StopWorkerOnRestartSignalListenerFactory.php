<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Listener;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

/**
 * @psalm-api
 */
final class StopWorkerOnRestartSignalListenerFactory
{
    public function __invoke(ContainerInterface $container): StopWorkerOnRestartSignalListener
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        /** @var string|null $logger */
        $logger = $config['messenger']['logger'] ?? null;

        $cachePoolForRestartSignal = $config['messenger']['cache_pool_for_restart_signal'] ?? null;

        if (! $cachePoolForRestartSignal) {
            throw new InvalidArgumentException('Invalid cache_pool_for_restart_signal service');
        }

        return new StopWorkerOnRestartSignalListener(
            $container->get($cachePoolForRestartSignal),
            $logger && $container->has($logger) ? $container->get($logger) : null,
        );
    }
}
