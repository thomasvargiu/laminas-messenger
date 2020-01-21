<?php

declare(strict_types=1);

namespace TMV\Messenger\Test\Middleware;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use stdClass;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;
use TMV\Messenger\Middleware\DoctrineClearEntityManagerMiddleware;

class DoctrineClearEntityManagerMiddlewareTest extends MiddlewareTestCase
{
    public function testMiddlewareClearEntityManager(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('clear');
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('default')
            ->willReturn($entityManager);
        $middleware = new DoctrineClearEntityManagerMiddleware($managerRegistry, 'default');
        $envelope = new Envelope(new stdClass(), [
            new ReceivedStamp('async'),
        ]);
        $middleware->handle($envelope, $this->getStackMock());
    }

    public function testInvalidEntityManagerThrowsException(): void
    {
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('unknown_manager')
            ->will($this->throwException(new InvalidArgumentException()));
        $middleware = new DoctrineClearEntityManagerMiddleware($managerRegistry, 'unknown_manager');
        $this->expectException(UnrecoverableMessageHandlingException::class);
        $middleware->handle(new Envelope(new stdClass()), $this->getStackMock(false));
    }

    public function testMiddlewareDoesNotClearInNonWorkerContext(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->never())
            ->method('clear');
        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->with('default')
            ->willReturn($entityManager);
        $middleware = new DoctrineClearEntityManagerMiddleware($managerRegistry, 'default');
        $envelope = new Envelope(new stdClass());
        $middleware->handle($envelope, $this->getStackMock());
    }
}
