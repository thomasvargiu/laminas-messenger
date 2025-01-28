<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Bridge\Doctrine\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

abstract class AbstractDoctrineMiddleware implements MiddlewareInterface
{
    protected ManagerRegistry $managerRegistry;

    protected ?string $entityManagerName;

    public function __construct(ManagerRegistry $managerRegistry, ?string $entityManagerName = null)
    {
        $this->managerRegistry = $managerRegistry;
        $this->entityManagerName = $entityManagerName;
    }

    final public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        try {
            $connection = $this->managerRegistry->getManager($this->entityManagerName);
        } catch (InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
        }

        return $this->handleForManager($connection, $envelope, $stack);
    }

    abstract protected function handleForManager(EntityManagerInterface $entityManager, Envelope $envelope, StackInterface $stack): Envelope;
}
