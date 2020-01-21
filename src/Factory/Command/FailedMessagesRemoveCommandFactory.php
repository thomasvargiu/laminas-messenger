<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;

final class FailedMessagesRemoveCommandFactory
{
    public function __invoke(ContainerInterface $container): FailedMessagesRemoveCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $failureTransportName = $config['messenger']['failure_transport'] ?? null;

        if (null === $failureTransportName) {
            throw new InvalidArgumentException('Invalid failure_transport name');
        }

        /** @var ContainerInterface $receiverLocator */
        $receiverLocator = $container->get('messenger.receivers_locator');

        return new FailedMessagesRemoveCommand(
            $failureTransportName,
            $receiverLocator->get($failureTransportName)
        );
    }
}
