<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Middleware\DoctrineClearEntityManagerMiddleware;

final class DoctrineClearEntityManagerMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new DoctrineClearEntityManagerMiddleware(
            $container->get(ManagerRegistry::class),
            $this->connectionName
        );
    }
}
