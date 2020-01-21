<?php

declare(strict_types=1);

namespace TMV\Messenger\Factory\Middleware;

use Doctrine\Common\Persistence\ManagerRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Messenger\Middleware\DoctrineTransactionMiddleware;

final class DoctrineTransactionMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new DoctrineTransactionMiddleware(
            $container->get(ManagerRegistry::class),
            $this->connectionName
        );
    }
}
