<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\EventListener\SendFailedMessageForRetryListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;

final class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container): EventDispatcherInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        $eventDispatcher = new EventDispatcher();

        $eventDispatcher->addSubscriber($container->get(SendFailedMessageForRetryListener::class));

        $failureTransport = $config['messenger']['failure_transport'] ?? null;

        if ($failureTransport) {
            $eventDispatcher->addSubscriber($container->get(SendFailedMessageToFailureTransportListener::class));
        }

        $cachePoolForRestartSignal = $config['messenger']['cache_pool_for_restart_signal'] ?? null;

        if ($cachePoolForRestartSignal) {
            $eventDispatcher->addSubscriber($container->get(StopWorkerOnRestartSignalListener::class));
        }

        return $eventDispatcher;
    }
}
