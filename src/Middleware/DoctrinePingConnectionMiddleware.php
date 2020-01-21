<?php

declare(strict_types=1);

namespace TMV\Messenger\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class DoctrinePingConnectionMiddleware extends AbstractDoctrineMiddleware
{
    protected function handleForManager(
        EntityManagerInterface $entityManager,
        Envelope $envelope,
        StackInterface $stack
    ): Envelope {
        if (null !== $envelope->last(ReceivedStamp::class)) {
            $this->pingConnection($entityManager);
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function pingConnection(EntityManagerInterface $entityManager): void
    {
        $connection = $entityManager->getConnection();

        if (! $connection->ping()) {
            $connection->close();
            $connection->connect();
        }

        if (! $entityManager->isOpen()) {
            $this->managerRegistry->resetManager($this->entityManagerName);
        }
    }
}
