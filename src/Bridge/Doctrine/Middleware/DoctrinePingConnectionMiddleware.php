<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

/**
 * @final
 */
class DoctrinePingConnectionMiddleware extends AbstractDoctrineMiddleware
{
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
            $this->executeDummySql($connection);
        } catch (Exception) {
            $connection->close();
            // Attempt to reestablish the lazy connection by sending another query.
            $this->executeDummySql($connection);
        }

        if (! $entityManager->isOpen()) {
            $this->managerRegistry->resetManager($this->entityManagerName);
        }
    }

    /**
     * @throws Exception
     */
    private function executeDummySql(Connection $connection): void
    {
        $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL());
    }
}
