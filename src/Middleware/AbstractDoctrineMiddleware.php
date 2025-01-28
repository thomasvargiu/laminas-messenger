<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Middleware;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @deprecated Use {@see \TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\AbstractDoctrineMiddleware}
 */
abstract class AbstractDoctrineMiddleware extends \TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\AbstractDoctrineMiddleware
{
    public function __construct(ManagerRegistry $managerRegistry, ?string $entityManagerName = null)
    {
        parent::__construct($managerRegistry, $entityManagerName);

        trigger_error(sprintf(
            'Class %s is deprecated, please use %s instead',
            self::class,
            \TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\AbstractDoctrineMiddleware::class
        ), E_USER_DEPRECATED);
    }
}
