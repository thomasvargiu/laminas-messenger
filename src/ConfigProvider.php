<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger;

use Laminas\ServiceManager\Factory\InvokableFactory;
use Symfony\Component\Messenger as SFMessenger;

class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                SFMessenger\Transport\Sender\SendersLocatorInterface::class => SFMessenger\Transport\Sender\SendersLocator::class,
                SFMessenger\Transport\TransportFactoryInterface::class => SFMessenger\Transport\TransportFactory::class,
            ],
            'factories' => [
                Transport\Doctrine\DoctrineDBALTransportFactory::class => Factory\Transport\Doctrine\DoctrineDBALTransportFactoryFactory::class,
                SFMessenger\Transport\InMemoryTransportFactory::class => InvokableFactory::class,
                SFMessenger\Transport\AmqpExt\AmqpTransportFactory::class => InvokableFactory::class,
                SFMessenger\Transport\Doctrine\DoctrineTransportFactory::class => InvokableFactory::class,
                SFMessenger\Transport\RedisExt\RedisTransportFactory::class => InvokableFactory::class,
                SFMessenger\Transport\Sync\SyncTransportFactory::class => Factory\Transport\Sync\SyncTransportFactoryFactory::class,
                SFMessenger\Transport\TransportFactory::class => Factory\Transport\TransportFactoryFactory::class,
                SFMessenger\Transport\Sender\SendersLocator::class => Factory\Transport\Sender\SendersLocatorFactory::class,
                SFMessenger\Transport\Serialization\PhpSerializer::class => InvokableFactory::class,
                SFMessenger\Transport\Serialization\Serializer::class => InvokableFactory::class,
                SFMessenger\EventListener\DispatchPcntlSignalListener::class => InvokableFactory::class,
                SFMessenger\EventListener\StopWorkerOnSigtermSignalListener::class => InvokableFactory::class,
                SFMessenger\EventListener\SendFailedMessageForRetryListener::class => Factory\Listener\SendFailedMessageForRetryListenerFactory::class,
                SFMessenger\EventListener\SendFailedMessageToFailureTransportListener::class => Factory\Listener\SendFailedMessageToFailureTransportListenerFactory::class,
                SFMessenger\EventListener\StopWorkerOnRestartSignalListener::class => Factory\Listener\StopWorkerOnRestartSignalListenerFactory::class,
                SFMessenger\Command\ConsumeMessagesCommand::class => Factory\Command\ConsumeMessagesCommandFactory::class,
                SFMessenger\Command\StopWorkersCommand::class => Factory\Command\StopWorkersCommandFactory::class,
                SFMessenger\Command\SetupTransportsCommand::class => Factory\Command\SetupTransportsCommandFactory::class,
                SFMessenger\Command\FailedMessagesRemoveCommand::class => Factory\Command\FailedMessagesRemoveCommandFactory::class,
                SFMessenger\Command\FailedMessagesRetryCommand::class => Factory\Command\FailedMessagesRetryCommandFactory::class,
                SFMessenger\Command\FailedMessagesShowCommand::class => Factory\Command\FailedMessagesShowCommandFactory::class,
                'messenger.bus.default' => [Factory\MessageBusFactory::class, 'messenger.bus.default'],
                'messenger.event_dispatcher' => Factory\EventDispatcherFactory::class,
                'messenger.routable_message_bus' => [Factory\RoutableMessageBusFactory::class, 'messenger.bus.default'],
                'messenger.retry_strategy_locator' => Factory\Retry\RetryStrategyLocatorFactory::class,
                'messenger.receivers_locator' => Factory\Transport\Receiver\ReceiversLocatorFactory::class,
                'messenger.transports_locator' => Factory\Transport\TransportProviderFactory::class,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'messenger' => [
                'failure_transport' => null,
                'event_dispatcher' => 'messenger.event_dispatcher',
                'logger' => null,
                'default_serializer' => SFMessenger\Transport\Serialization\PhpSerializer::class,
                'cache_pool_for_restart_signal' => null,
                'transport_factories' => [
                    SFMessenger\Transport\Sync\SyncTransportFactory::class,
                    SFMessenger\Transport\InMemoryTransportFactory::class,
                    SFMessenger\Transport\AmqpExt\AmqpTransportFactory::class,
                    SFMessenger\Transport\RedisExt\RedisTransportFactory::class,
                    Transport\Doctrine\DoctrineDBALTransportFactory::class,
                ],
                'buses' => [
                    'messenger.bus.default' => [
                        'middleware' => [],
                        'default_middleware' => true,
                        'allow_no_handler' => false,
                        'handlers' => [],
                        'routes' => [],
                    ],
                ],
                'transports' => [],
            ],
        ];
    }
}
