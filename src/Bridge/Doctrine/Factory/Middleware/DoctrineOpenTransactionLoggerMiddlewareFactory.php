<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrineOpenTransactionLoggerMiddleware;

/**
 * @api
 *
 * @final
 */
class DoctrineOpenTransactionLoggerMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        $logger = $this->getConfig($container)['logger'] ?? null;

        return new DoctrineOpenTransactionLoggerMiddleware(
            $this->getManagerRegistry($container),
            $this->getName(),
            $logger && $container->has($logger) ? $container->get($logger) : null,
        );
    }
}
