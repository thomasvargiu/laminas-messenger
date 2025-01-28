<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\StackInterface;

/**
 * @final
 */
class DoctrineOpenTransactionLoggerMiddleware extends AbstractDoctrineMiddleware
{
    private ?LoggerInterface $logger;

    private bool $isHandling = false;

    public function __construct(
        ManagerRegistry $managerRegistry,
        ?string $entityManagerName = null,
        ?LoggerInterface $logger = null,
    ) {
        parent::__construct($managerRegistry, $entityManagerName);
        $this->logger = $logger;
    }

    protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope
    {
        if ($this->isHandling) {
            return $stack->next()->handle($envelope, $stack);
        }

        $this->isHandling = true;
        $initialTransactionLevel = $entityManager->getConnection()->getTransactionNestingLevel();

        try {
            return $stack->next()->handle($envelope, $stack);
        } finally {
            if ($this->logger && $entityManager->getConnection()->getTransactionNestingLevel() > $initialTransactionLevel) {
                $this->logger->error('A handler opened a transaction but did not close it.', [
                    'message' => $envelope->getMessage(),
                ]);
            }
            $this->isHandling = false;
        }
    }
}
