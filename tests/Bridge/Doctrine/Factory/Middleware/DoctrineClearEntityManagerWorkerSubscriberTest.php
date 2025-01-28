<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Bridge\Doctrine\Factory\Middleware;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use TMV\Laminas\Messenger\Bridge\Doctrine\Middleware\DoctrineClearEntityManagerWorkerSubscriber;

class DoctrineClearEntityManagerWorkerSubscriberTest extends TestCase
{
    use ProphecyTrait;

    public function testSubscribedEvents(): void
    {
        $managerRegistry = $this->prophesize(ManagerRegistry::class);
        $subscriber = new DoctrineClearEntityManagerWorkerSubscriber($managerRegistry->reveal());

        $events = [];
        foreach (DoctrineClearEntityManagerWorkerSubscriber::getSubscribedEvents() as $key => $value) {
            $events[$key] = $value;
            $this->assertIsCallable([$subscriber, $value]);
        }

        $this->assertSame([
            WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
            WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
        ], $events);
    }

    public function testOnWorkerMessageHandled(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $managerRegistry->getManagers()->willReturn([$entityManager->reveal()]);
        $entityManager->clear()->shouldBeCalled();

        $subscriber = new DoctrineClearEntityManagerWorkerSubscriber($managerRegistry->reveal());
        $subscriber->onWorkerMessageHandled();
    }

    public function testOnWorkerMessageFailed(): void
    {
        $entityManager = $this->prophesize(EntityManagerInterface::class);
        $managerRegistry = $this->prophesize(ManagerRegistry::class);

        $managerRegistry->getManagers()->willReturn([$entityManager->reveal()]);
        $entityManager->clear()->shouldBeCalled();

        $subscriber = new DoctrineClearEntityManagerWorkerSubscriber($managerRegistry->reveal());
        $subscriber->onWorkerMessageFailed();
    }
}
