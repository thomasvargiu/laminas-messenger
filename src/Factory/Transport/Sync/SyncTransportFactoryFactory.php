<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport\Sync;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransportFactory;

final class SyncTransportFactoryFactory
{
    public function __invoke(ContainerInterface $container): SyncTransportFactory
    {
        return new SyncTransportFactory(
            $container->get('messenger.routable_message_bus')
        );
    }
}
