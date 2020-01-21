<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Command;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesShowCommand;
use TMV\Messenger\Exception\InvalidArgumentException;

final class FailedMessagesShowCommandFactory
{
    public function __invoke(ContainerInterface $container): FailedMessagesShowCommand
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $failureTransportName = $config['messenger']['failure_transport'] ?? null;

        if (null === $failureTransportName) {
            throw new InvalidArgumentException('Invalid failure_transport name');
        }

        /** @var ContainerInterface $receiverLocator */
        $receiverLocator = $container->get('messenger.receivers_locator');

        return new FailedMessagesShowCommand(
            $failureTransportName,
            $receiverLocator->get($failureTransportName)
        );
    }
}
