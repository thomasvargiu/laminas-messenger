<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Middleware;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrinePingConnectionMiddleware;

class DoctrinePingConnectionMiddlewareTest extends MiddlewareTestCase
{
    /** @var Connection&MockObject */
    private $connection;

    /** @var EntityManagerInterface&MockObject */
    private $entityManager;

    /** ManagerRegistry&MockObject */
    private $managerRegistry;

    private DoctrinePingConnectionMiddleware $middleware;

    private string $entityManagerName = 'default';

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

    public function testMiddlewarePingOk()
    {
        $this->connection->method('getDatabasePlatform')
            ->willReturn($this->mockPlatform());

        $this->connection->expects($this->exactly(2))
            ->method('executeQuery')
            ->willReturnCallback(function () {
                static $counter = 0;

                if (1 === ++$counter) {
                    throw $this->createMock(DBALException::class);
                }

                return $this->createMock(Result::class);
            });

        $this->connection->expects($this->once())
            ->method('close')
        ;

        $envelope = new Envelope(new \stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testMiddlewarePingResetEntityManager()
    {
        $this->connection->method('getDatabasePlatform')
            ->willReturn($this->mockPlatform());

        $this->entityManager->expects($this->once())
            ->method('isOpen')
            ->willReturn(false)
        ;
        $this->managerRegistry->expects($this->once())
            ->method('resetManager')
            ->with($this->entityManagerName)
        ;

        $envelope = new Envelope(new \stdClass(), [
            new ConsumedByWorkerStamp(),
        ]);
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    public function testInvalidEntityManagerThrowsException()
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('unknown_manager')
            ->willThrowException(new \InvalidArgumentException());

        $middleware = new DoctrinePingConnectionMiddleware($managerRegistry, 'unknown_manager');

        $this->expectException(UnrecoverableMessageHandlingException::class);

        $middleware->handle(new Envelope(new \stdClass()), $this->getStackMock(false));
    }

    public function testMiddlewareNoPingInNonWorkerContext()
    {
        $this->connection->expects($this->never())
            ->method('close')
        ;

        $envelope = new Envelope(new \stdClass());
        $this->middleware->handle($envelope, $this->getStackMock());
    }

    /**
     * @return AbstractPlatform&MockObject
     */
    private function mockPlatform()
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->method('getDummySelectSQL')->willReturn('SELECT 1');

        return $platform;
    }
}
