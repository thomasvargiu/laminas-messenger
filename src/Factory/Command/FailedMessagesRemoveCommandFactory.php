<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Command;

use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Command\FailedMessagesRemoveCommand;
use Symfony\Contracts\Service\ServiceProviderInterface;
use TMV\Laminas\Messenger\Exception\InvalidArgumentException;
use TMV\Laminas\Messenger\ServiceProvider;

final class FailedMessagesRemoveCommandFactory
{
    public function __invoke(ContainerInterface $container): FailedMessagesRemoveCommand
    {
        /** @var array{messenger: array{failure_transport?: string}} $config */
        $config = $container->has('config') ? $container->get('config') : [];
        $failureTransportName = $config['messenger']['failure_transport'] ?? null;

        if (null === $failureTransportName) {
            throw new InvalidArgumentException('Invalid failure_transport name');
        }

        /** @var ServiceProviderInterface $receiverLocator */
        $receiverLocator = $container->get('messenger.receivers_locator');

        return new FailedMessagesRemoveCommand(
            $failureTransportName,
            new ServiceProvider([
                $failureTransportName => static function () use ($receiverLocator, $failureTransportName) { return $receiverLocator->get($failureTransportName); },
            ]),
        );
    }
}
