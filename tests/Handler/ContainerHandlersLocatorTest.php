<?php

declare(strict_types=1);

namespace TMV\Laminas\Messenger\Test\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use TMV\Laminas\Messenger\Handler\ContainerHandlersLocator;
use TMV\Laminas\Messenger\Test\Factory\MessageMock;

class HandlersLocatorTest extends TestCase
{
    public function testItYieldsHandlerDescriptors(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $handler = $this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']);
        $skippedHandler = $this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']);
        $locator = new ContainerHandlersLocator($container->reveal(), [
            MessageMock::class => [$handler, $skippedHandler],
        ]);

        $this->assertEquals([new HandlerDescriptor($handler)], iterator_to_array($locator->getHandlers(new Envelope(new MessageMock()))));
    }

    public function testItYieldsHandlerDescriptorsFromContainer(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $handler = $this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']);

        $container->has('foo-handler')->willReturn(true);
        $container->get('foo-handler')->willReturn($handler);

        $locator = new ContainerHandlersLocator($container->reveal(), [
            MessageMock::class => ['foo-handler'],
        ]);

        $this->assertEquals([new HandlerDescriptor($handler)], iterator_to_array($locator->getHandlers(new Envelope(new MessageMock()))));
    }

    public function testItReturnsOnlyHandlersMatchingTransport(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $firstHandler = $this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']);
        $secondHandler = $this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']);

        $locator = new ContainerHandlersLocator($container->reveal(), [
            MessageMock::class => [
                $first = new HandlerDescriptor($firstHandler, ['alias' => 'one']),
                new HandlerDescriptor($this->createPartialMock(HandlersLocatorTestCallable::class, ['__invoke']), ['from_transport' => 'ignored', 'alias' => 'two']),
                $second = new HandlerDescriptor($secondHandler, ['from_transport' => 'transportName', 'alias' => 'three']),
            ],
        ]);

        $this->assertEquals([
            $first,
            $second,
        ], iterator_to_array($locator->getHandlers(
            new Envelope(new MessageMock('Body'), [new ReceivedStamp('transportName')])
        )));
    }
}

class HandlersLocatorTestCallable
{
    public function __invoke()
    {
    }
}
