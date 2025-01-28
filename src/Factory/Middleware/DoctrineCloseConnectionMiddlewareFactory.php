<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Factory\Middleware;

/**
 * @deprecated Use {@see \TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware\DoctrineCloseConnectionMiddlewareFactory}
 */
class DoctrineCloseConnectionMiddlewareFactory extends \TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware\DoctrineCloseConnectionMiddlewareFactory
{
    public function __construct(?string $connectionName = null)
    {
        parent::__construct($connectionName);

        trigger_error(sprintf(
            'Class %s is deprecated, please use %s instead',
            self::class,
            \TMV\Laminas\Messenger\Bridge\Doctrine\Factory\Middleware\DoctrineCloseConnectionMiddlewareFactory::class
        ), E_USER_DEPRECATED);
    }
}
