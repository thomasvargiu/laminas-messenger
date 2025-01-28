<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrineTransactionMiddleware;

class DoctrineTransactionMiddlewareTest extends MiddlewareTestCase
{
    /** @var MockObject&Connection */
    private $connection;

    /** @var MockObject&EntityManagerInterface */
    private $entityManager;

    private DoctrineTransactionMiddleware $middleware;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($this->entityManager);

        $this->middleware = new DoctrineTransactionMiddleware($managerRegistry);
    }

    public function testMiddlewareWrapsInTransactionAndFlushes()
    {
        $this->connection->expects($this->once())
            ->method('beginTransaction')
        ;
        $this->connection->expects($this->once())
            ->method('commit')
        ;
        $this->entityManager->expects($this->once())
            ->method('flush')
        ;

        $this->middleware->handle(new Envelope(new \stdClass()), $this->getStackMock());
    }

    public function testTransactionIsRolledBackOnException()
    {
        $this->connection->expects($this->once())->method('beginTransaction');
        $this->connection->expects($this->once())->method('isTransactionActive')->willReturn(true);
        $this->connection->expects($this->once())->method('rollBack');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Thrown from next middleware.');

        $this->middleware->handle(new Envelope(new \stdClass()), $this->getThrowingStackMock());
    }

    public function testExceptionInRollBackDoesNotHidePreviousException()
    {
        $this->connection->expects($this->once())->method('beginTransaction');
        $this->connection->expects($this->once())->method('isTransactionActive')->willReturn(true);
        $this->connection->expects($this->once())->method('rollBack')->willThrowException(new \RuntimeException('Thrown from rollBack.'));

        try {
            $this->middleware->handle(new Envelope(new \stdClass()), $this->getThrowingStackMock());
        } catch (\Throwable $exception) {
        }

        self::assertNotNull($exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertSame('Thrown from rollBack.', $exception->getMessage());

        $previous = $exception->getPrevious();
        self::assertNotNull($previous);
        self::assertInstanceOf(\RuntimeException::class, $previous);
        self::assertSame('Thrown from next middleware.', $previous->getMessage());
    }

    public function testInvalidEntityManagerThrowsException()
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('unknown_manager')
            ->willThrowException(new \InvalidArgumentException());

        $middleware = new DoctrineTransactionMiddleware($managerRegistry, 'unknown_manager');

        $this->expectException(UnrecoverableMessageHandlingException::class);

        $middleware->handle(new Envelope(new \stdClass()), $this->getStackMock(false));
    }
}
