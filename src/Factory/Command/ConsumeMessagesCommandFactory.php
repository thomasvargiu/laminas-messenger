<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Command;

use function array_keys;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\ConsumeMessagesCommand;
use Symfony\Component\Messenger\RoutableMessageBus;

final class ConsumeMessagesCommandFactory
{
    public function __invoke(ContainerInterface $container): ConsumeMessagesCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        /** @var RoutableMessageBus $messageBus */
        $messageBus = $container->get('messenger.routable_message_bus');
        $eventDispatcher = $config['messenger']['event_dispatcher'] ?? 'messenger.event_dispatcher';
        $logger = $config['messenger']['logger'] ?? null;
        $transports = $config['messenger']['transports'] ?? [];

        $command = new ConsumeMessagesCommand(
            $messageBus,
            $container->get('messenger.receivers_locator'),
            $container->get($eventDispatcher),
            $logger ? $container->get($logger) : null,
            array_keys($transports)
        );

        return $command;
    }
}
