<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\AbstractLogger;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrineOpenTransactionLoggerMiddleware;

class DoctrineOpenTransactionLoggerMiddlewareTest extends MiddlewareTestCase
{
    private AbstractLogger $logger;

    /** @var MockObject&Connection */
    private $connection;

    /** @var MockObject&EntityManagerInterface */
    private $entityManager;

    private DoctrineOpenTransactionLoggerMiddleware $middleware;

    protected function setUp(): void
    {
        $this->logger = new class extends AbstractLogger {
            public array $logs = [];

            public function log($level, $message, $context = []): void
            {
                $this->logs[$level][] = $message;
            }
        };

        $this->connection = $this->createMock(Connection::class);

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry->method('getManager')->willReturn($this->entityManager);

        $this->middleware = new DoctrineOpenTransactionLoggerMiddleware($managerRegistry, null, $this->logger);
    }

    public function testMiddlewareWrapsInTransactionAndFlushes()
    {
        $this->connection->expects($this->exactly(2))
            ->method('getTransactionNestingLevel')
            ->willReturn(0, 1)
        ;

        $this->middleware->handle(new Envelope(new \stdClass()), $this->getStackMock());

        $this->assertSame(['error' => ['A handler opened a transaction but did not close it.']], $this->logger->logs);
    }
}
