<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

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
            if (null !== $envelope->last(ReceivedStamp::class)) {
                $entityManager->clear();
            }
        }
    }
}
