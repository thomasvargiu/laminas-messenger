<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Middleware;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Prophecy\PhpUnit\ProphecyTrait;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Laminas\Messenger\Middleware\DoctrinePingConnectionMiddleware;

class DoctrinePingConnectionMiddlewareTest extends MiddlewareTestCase
{
    use ProphecyTrait;

    private $connection;

    private $entityManager;

    private $managerRegistry;

    private $middleware;

    private $entityManagerName = 'default';

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->entityManager->method('getConnection')->willReturn($this->connection);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry->method('getManager')->willReturn($this->entityManager);
        $this->middleware = new DoctrinePingConnectionMiddleware(
            $this->managerRegistry,
            $this->entityManagerName
        );
    }

    public function testMiddlewareWithCustomQuery(): void
    {
        $this->middleware = new DoctrinePingConnectionMiddleware(
            $this->managerRegistry,
            $this->entityManagerName,
            'SELECT 2;'
        );
        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 2;');
        $this->connection->expects($this->never())
            ->method('close');
        $envelope = new Envelope(new stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testMiddlewarePingOk(): void
    {
        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1;');
        $this->connection->expects($this->never())
            ->method('close');
        $envelope = new Envelope(new stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testMiddlewarePingFail(): void
    {
        $this->connection->expects($this->once())
            ->method('executeQuery')
            ->with('SELECT 1;')
            ->willThrowException(new \Exception('Error'));
        $this->connection->expects($this->once())
            ->method('close');
        $envelope = new Envelope(new stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testMiddlewarePingResetEntityManager(): void
    {
        $this->entityManager->expects($this->once())
            ->method('isOpen')
            ->willReturn(false);
        $this->managerRegistry->expects($this->once())
            ->method('resetManager')
            ->with($this->entityManagerName);
        $envelope = new Envelope(new stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testInvalidEntityManagerThrowsException(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('unknown_manager')
            ->will($this->throwException(new InvalidArgumentException()));
        $middleware = new DoctrinePingConnectionMiddleware($managerRegistry, 'unknown_manager');
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $middleware->handle(new Envelope(new stdClass()), $this->getStackMock(false));
    }

    public function testMiddlewareNoPingInNonWorkerContext(): void
    {
        $this->connection->expects($this->never())
            ->method('executeQuery')
            ->with('SELECT 1;');
        $this->connection->expects($this->never())
            ->method('close');
        $envelope = new Envelope(new stdClass());
        $this->middleware->handle($envelope, $this->getStackMock());
    }
}
