<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Transport\Doctrine;

use Psr\Container\ContainerInterface;
use TMV\Laminas\Messenger\Transport\Doctrine\DoctrineDBALTransportFactory;

final class DoctrineDBALTransportFactoryFactory
{
    public function __invoke(ContainerInterface $container): DoctrineDBALTransportFactory
    {
        return new DoctrineDBALTransportFactory($container);
    }
}
