<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

final class DoctrinePingConnectionMiddleware extends AbstractDoctrineMiddleware
{
    private string $query;

    public function __construct(ManagerRegistry $managerRegistry, string $entityManagerName, string $query = 'SELECT 1;')
    {
        parent::__construct($managerRegistry, $entityManagerName);
        $this->query = $query;
    }

    protected function handleForManager(
        EntityManagerInterface $entityManager,
        Envelope $envelope,
        StackInterface $stack
    ): Envelope {
        if (null !== $envelope->last(ConsumedByWorkerStamp::class)) {
            $this->pingConnection($entityManager);
        }

        return $stack->next()->handle($envelope, $stack);
    }

    private function pingConnection(EntityManagerInterface $entityManager): void
    {
        $connection = $entityManager->getConnection();

        try {
            $connection->executeQuery($this->query);
        } catch (\Throwable $exception) {
            $connection->close();
        }

        if (! $entityManager->isOpen()) {
            $this->managerRegistry->resetManager($this->entityManagerName);
        }
    }
}
