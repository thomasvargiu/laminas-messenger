<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Subscriber;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;

class DoctrineClearEntityManagerWorkerSubscriber implements EventSubscriberInterface
{
    /** @var ManagerRegistry */
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function onWorkerMessageHandled(): void
    {
        $this->clearEntityManagers();
    }

    public function onWorkerMessageFailed(): void
    {
        $this->clearEntityManagers();
    }

    /**
     * @return iterable<string, string>
     */
    public static function getSubscribedEvents(): iterable
    {
        yield WorkerMessageHandledEvent::class => 'onWorkerMessageHandled';
        yield WorkerMessageFailedEvent::class => 'onWorkerMessageFailed';
    }

    private function clearEntityManagers(): void
    {
        foreach ($this->managerRegistry->getManagers() as $manager) {
            $manager->clear();
        }
    }
}
