<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

/**
 * @deprecated This middleware is deprecated and will be removed in 2.0.
 */
class DoctrineClearEntityManagerMiddleware extends AbstractDoctrineMiddleware
{
    protected function handleForManager(
        EntityManagerInterface $entityManager,
        Envelope $envelope,
        StackInterface $stack
    ): Envelope {
        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            if (null !== $envelope->last(ConsumedByWorkerStamp::class)) {
                $entityManager->clear();
            }
        }
    }
}
