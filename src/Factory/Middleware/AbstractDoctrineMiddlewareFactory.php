<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;

abstract class AbstractDoctrineMiddlewareFactory
{
    protected ?string $connectionName;

    public function __construct(string $connectionName = null)
    {
        $this->connectionName = $connectionName;
    }

    abstract public function __invoke(ContainerInterface $container): MiddlewareInterface;
}
