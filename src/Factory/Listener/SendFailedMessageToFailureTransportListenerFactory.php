<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Listener;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\EventListener\SendFailedMessageToFailureTransportListener;
use TMV\Messenger\Exception\InvalidArgumentException;

final class SendFailedMessageToFailureTransportListenerFactory
{
    public function __invoke(ContainerInterface $container): SendFailedMessageToFailureTransportListener
    {
        /** @var array<string, mixed> $config */
        $config = $container->has('config') ? $container->get('config') : [];

        /** @var string|null $failureTransportName */
        $failureTransportName = $config['messenger']['failure_transport'] ?? null;

        if (null === $failureTransportName) {
            throw new InvalidArgumentException('Invalid failure_transport name');
        }

        /** @var string|null $logger */
        $logger = $config['messenger']['logger'] ?? null;

        return new SendFailedMessageToFailureTransportListener(
            $container->get($failureTransportName),
            $logger ? $container->get($logger) : null
        );
    }
}
