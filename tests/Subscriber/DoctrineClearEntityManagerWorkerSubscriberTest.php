<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Subscriber;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use TMV\Laminas\Messenger\Subscriber\DoctrineClearEntityManagerWorkerSubscriber;

class DoctrineClearEntityManagerWorkerSubscriberTest extends TestCase
{
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
