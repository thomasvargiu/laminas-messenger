<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Laminas\Messenger\Middleware\DoctrineTransactionMiddleware;

class DoctrineTransactionMiddlewareTest extends MiddlewareTestCase
{
    private $connection;

    private $entityManager;

    private $middleware;

    public function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($this->entityManager);
        $this->middleware = new DoctrineTransactionMiddleware($managerRegistry);
    }

    public function testMiddlewareWrapsInTransactionAndFlushes(): void
    {
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('commit');
        $this->entityManager->expects($this->once())
            ->method('flush');
        $this->middleware->handle(new Envelope(new stdClass()), $this->getStackMock());
    }

    public function testTransactionIsRolledBackOnException(): void
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Thrown from next middleware.');
        $this->connection->expects($this->once())
            ->method('beginTransaction');
        $this->connection->expects($this->once())
            ->method('rollBack');
        $this->middleware->handle(new Envelope(new stdClass()), $this->getThrowingStackMock());
    }

    public function testInvalidEntityManagerThrowsException(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('unknown_manager')
            ->will($this->throwException(new InvalidArgumentException()));
        $middleware = new DoctrineTransactionMiddleware($managerRegistry, 'unknown_manager');
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $middleware->handle(new Envelope(new stdClass()), $this->getStackMock(false));
    }
}
