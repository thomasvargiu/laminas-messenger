<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrineClearEntityManagerMiddleware;

/**
 * @api
 *
 * @final
 */
class DoctrineClearEntityManagerMiddlewareFactory extends AbstractDoctrineMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new DoctrineClearEntityManagerMiddleware(
            $this->getManagerRegistry($container),
            $this->getName()
        );
    }
}
