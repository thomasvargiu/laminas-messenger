<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesRetryCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\ServiceProvider;

final class FailedMessagesRetryCommandFactory
{
    public function __invoke(ContainerInterface $container): FailedMessagesRetryCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var string|null $failureTransportName */
        $failureTransportName = $config['messenger']['failure_transport'] ?? null;

        if (null === $failureTransportName) {
            throw new InvalidArgumentException('Invalid failure_transport name');
        }

        $eventDispatcher = $config['messenger']['event_dispatcher'] ?? 'messenger.event_dispatcher';

        if (null === $eventDispatcher) {
            throw new InvalidArgumentException('Invalid event_dispatcher service');
        }

        /** @var ServiceProviderInterface $receiverLocator */
        $receiverLocator = $container->get('messenger.receivers_locator');
        /** @var MessageBusInterface $messageBus */
        $messageBus = $container->get('messenger.routable_message_bus');
        /** @var string|null $logger */
        $logger = $config['messenger']['logger'] ?? null;

        return new FailedMessagesRetryCommand(
            $failureTransportName,
            new ServiceProvider([
                $failureTransportName => static function () use ($receiverLocator, $failureTransportName) { return $receiverLocator->get($failureTransportName); },
            ]),
            $messageBus,
            $container->get($eventDispatcher),
            $logger ? $container->get($logger) : null
        );
    }
}
