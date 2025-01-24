<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\EventListener\DispatchPcntlSignalListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageForRetryListener;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnRestartSignalListener;
use Symfony\Component\Messenger\EventListener\StopWorkerOnSigtermSignalListener;

use function array_map;
use function array_merge;

/**
 * @psalm-api
 */
final class EventDispatcherFactory
{
    public function __invoke(ContainerInterface $container): EventDispatcherInterface
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        $eventDispatcher = new EventDispatcher();

        /** @var EventSubscriberInterface[] $subscribers */
        $subscribers = [
            DispatchPcntlSignalListener::class,
            StopWorkerOnSigtermSignalListener::class,
            SendFailedMessageForRetryListener::class,
        ];

        $failureTransport = $config['messenger']['failure_transport'] ?? null;

        if (null !== $failureTransport) {
            $subscribers[] = SendFailedMessageToFailureTransportListener::class;
        }

        $cachePoolForRestartSignal = $config['messenger']['cache_pool_for_restart_signal'] ?? null;

        if ($cachePoolForRestartSignal) {
            $subscribers[] = StopWorkerOnRestartSignalListener::class;
        }

        /** @var string[] $subscribers */
        $subscribers = array_merge(
            $config['messenger']['subscribers'] ?? [],
            $subscribers
        );

        array_map([$eventDispatcher, 'addSubscriber'], array_map([$container, 'get'], $subscribers));

        return $eventDispatcher;
    }
}
